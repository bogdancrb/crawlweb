<?php

/**
 * PHP-Proxy is a web-based proxy script designed to be fast, easy to customize,
 * and be able to provide support for complex sites such as YouTube and Facebook.
 * There have been many other proxy software scripts in the past, such as Glype, PHProxy,
 * CGIProxy, Surrogafier, ASProxy, Zelune... but all have either perished permanently or the creator
 * has stopped updating them. This proxy script is intended to replace all others.
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Source: https://github.com/Athlon1600/php-proxy-app
 *
 * Created by: https://www.php-proxy.com/
 * Modified by: Bogdan Corbeanu
 */

namespace ProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Proxy\Http\Request as ProxyRequest;
use Proxy\Http\Response as ProxyResponse;
use Proxy\Plugin\AbstractPlugin;
use Proxy\Event\FilterEvent;
use Proxy\Config;
use Proxy\Proxy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

class ProxyController extends Controller
{
    /**
     * @Route("/proxy", name="proxy_index")
     */
    public function indexAction()
    {
        $this->initConfig();

        if(!Config::get('app_key')){
            die("app_key inside config.php cannot be empty!");
        }

        if(!function_exists('curl_version')){
            die("cURL extension is not loaded!");
        }

        // how are our URLs be generated from this point? this must be set here so the proxify_url function below can make use of it
        if(Config::get('url_mode') == 2){
            Config::set('encryption_key', md5(Config::get('app_key').$_SERVER['REMOTE_ADDR']));
        } else if(Config::get('url_mode') == 3){
            Config::set('encryption_key', md5(Config::get('app_key').session_id()));
        }

        $fileLocator = $this->container->get('file_locator');
        $templatePath = $fileLocator->locate('@ProxyBundle/Templates/');
        $pluginsPath = $fileLocator->locate('@ProxyBundle/Plugins/');

        if(!isset($_GET['q'])){

            // must be at homepage - should we redirect somewhere else?
            if(Config::get('index_redirect')){

                // redirect to...
                header("HTTP/1.1 302 Found");
                header("Location: ".Config::get('index_redirect'));
            } else {
                return $this->render('admin/proxy_first_page.html.twig', [
                    'url' => '',
                    'version' => Proxy::VERSION
                ]);
            }

            exit;
        }

        // decode q parameter to get the real URL
        $url = url_decrypt($_GET['q']);

        $proxy = new Proxy();

        // load plugins
        foreach(Config::get('plugins', array()) as $plugin){

            $plugin_class = $plugin.'Plugin';

            if(file_exists($pluginsPath.$plugin_class.'.php')){

                // use user plugin from /plugins/
                require_once($pluginsPath.$plugin_class.'.php');

            } else if(class_exists('\\Proxy\\Plugin\\'.$plugin_class)){

                // does the native plugin from php-proxy package with such name exist?
                $plugin_class = '\\Proxy\\Plugin\\'.$plugin_class;
            }

            // otherwise plugin_class better be loaded already through composer.json and match namespace exactly \\Vendor\\Plugin\\SuperPlugin
            $proxy->getEventDispatcher()->addSubscriber(
                strstr("UrlFormPlugin", $plugin_class)
                || strstr("YoutubePlugin", $plugin_class)
                    ? new $plugin_class($templatePath)
                    : new $plugin_class()
            );
        }

        try {

            // request sent to index.php
            $request = ProxyRequest::createFromGlobals();

            // remove all GET parameters such as ?q=
            $request->get->clear();

            // forward it to some other URL
            $response = $proxy->forward($request, $url);

            // if that was a streaming response, then everything was already sent and script will be killed before it even reaches this line
            $response->send();

        } catch (\Exception $ex){

            // if the site is on server2.proxy.com then you may wish to redirect it back to proxy.com
            if(Config::get("error_redirect")){

                $url = render_string(Config::get("error_redirect"), array(
                    'error_msg' => rawurlencode($ex->getMessage())
                ));

                // Cannot modify header information - headers already sent
                header("HTTP/1.1 302 Found");
                header("Location: {$url}");

            } else {
                return $this->render('admin/proxy_first_page.html.twig', [
                    'url' => $url,
                    'error_msg' => $ex->getMessage(),
                    'version' => Proxy::VERSION
                ]);
            }
        }

        return new Response();
    }

    /**
     * @Route("/proxy/confirm", name="proxy_confirm")
     * @Method({"POST"})
     * @param Request $request
     */
    public function postFormDataAction(Request $request)
    {
        $reqUrl = $request->request->get('url');
        $this->initConfig();

        if(!Config::get('app_key')){
            die("app_key inside config.php cannot be empty!");
        }

        if(!function_exists('curl_version')){
            die("cURL extension is not loaded!");
        }

        // how are our URLs be generated from this point? this must be set here so the proxify_url function below can make use of it
        if(Config::get('url_mode') == 2){
            Config::set('encryption_key', md5(Config::get('app_key').$_SERVER['REMOTE_ADDR']));
        } else if(Config::get('url_mode') == 3){
            Config::set('encryption_key', md5(Config::get('app_key').session_id()));
        }

        if(isset($reqUrl)){

            $url = $reqUrl;
            $url = add_http($url);

            header("HTTP/1.1 302 Found");
            header('Location: ' . proxify_url($url));
            exit;
        }
    }

	/**
     * Initialize the configuration values
     */
    private function initConfig()
    {
        if (!empty($this->container->getParameter('proxy.app_key')))
        {
            Config::set('app_key', $this->container->getParameter('proxy.app_key'));
        }

        if (!empty($this->container->getParameter('proxy.url_mode')))
        {
            Config::set('url_mode', $this->container->getParameter('proxy.url_mode'));
        }

        if (!empty($this->container->getParameter('proxy.index_redirect')))
        {
            Config::set('index_redirect', $this->container->getParameter('proxy.index_redirect'));
        }

        if (!empty($this->container->getParameter('proxy.error_redirect')))
        {
            Config::set('error_redirect', $this->container->getParameter('proxy.error_redirect'));
        }

        if (!empty($this->container->getParameter('proxy.plugins')))
        {
            Config::set('plugins', $this->container->getParameter('proxy.plugins'));
        }
        
        if (!empty($this->container->getParameter('proxy.curl')))
        {
            $proxyOptions = $this->container->getParameter('proxy.curl');
            $resultProxyOptions = [];

            foreach ($proxyOptions as $key => $value)
            {
                switch ($key)
                {
                    case 'CURLOPT_PROXY':
                        $key = CURLOPT_PROXY;
                        break;
                    case 'CURLOPT_PORT':
                        $key = CURLOPT_PORT;
                        break;
                    case 'CURLOPT_HTTPPROXYTUNNEL':
                        $key = CURLOPT_HTTPPROXYTUNNEL;
                        break;
                    case 'CURLOPT_CONNECTTIMEOUT':
                        $key = CURLOPT_CONNECTTIMEOUT;
                        break;
                }

                $resultProxyOptions[$key] = $value;
            }

            Config::set('curl', $resultProxyOptions);
        }
    }
}
