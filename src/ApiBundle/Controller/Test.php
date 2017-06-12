<?php

namespace ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

class Test extends Controller
{
	public function postProxyAction(Request $request)
	{
		// Forbid every request but jquery's XHR
		if (!$request->isXmlHttpRequest()) {// isn't it an Ajax request?
			return new Response('not ajax request', 404,
				array('Content-Type' => 'application/json'));
		}

		$restUrl = $request->request->get('restUrl');
		$method = $request->request->get('method');
		$params = $request->request->get('params');
		$contentType = $request->request->get('contentType');
//		$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

		$headers = array(
			"Accept-Language: en-us",
			"User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
			"Connection: Keep-Alive",
			"Cache-Control: no-cache"
		);

		$referer = 'http://www.google.com/search';

		if ($contentType == null) {
			$contentType = 'application/json';
		}

		if ($restUrl == null || $method == null ||
			!in_array($method, array('GET', 'POST', 'DELETE'))) {
			return new Response('', 404, array('Content-Type' => $contentType));
		}

		session_write_close();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $restUrl);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if ($params != null) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
//		curl_setopt($ch, CURLOPT_USERAGENT, $agent);

		$requestCookies = $request->cookies->all();

		$cookieArray = array();
		foreach ($requestCookies as $cookieName => $cookieValue) {
			$cookieArray[] = "{$cookieName}={$cookieValue}";
		}
		$cookie_string = implode('; ', $cookieArray);
		curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);

		$response = curl_exec($ch);


//		$info = curl_getinfo($ch);
//		print_r($info['request_header']);
//		die;

		curl_close($ch);

		list($headers, $response) = explode("\r\n\r\n",$response,2);
		preg_match_all('/Set-Cookie: (.*)\b/', $headers, $cookies);
		$cookies = $cookies[1];

		if ($response === false) {
			return new Response('error', 404, array('Content-Type' => $contentType));
		} else {
			$response = new Response($response, 200,
				array('Content-Type' => $contentType));
			foreach($cookies as $rawCookie) {
				$cookie = \Symfony\Component\BrowserKit\Cookie::fromString($rawCookie);
				$value = $cookie->getValue();
				if (!empty($value)) {
					$value = str_replace(' ', '+', $value);
				}
				$customCookie = new Cookie($cookie->getName(), $value, $cookie->getExpiresTime()==null?0:$cookie->getExpiresTime(), $cookie->getPath());
				$response->headers->setCookie($customCookie);
			}
			return $response;
		}
	}
}