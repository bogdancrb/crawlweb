<?php

/**
 * PHP-Proxy is a web-based proxy script designed to be fast, easy to customize,
 * and be able to provide support for complex sites such as YouTube and Facebook.
 * There have been many other proxy software scripts in the past, such as Glype, PHProxy,
 * CGIProxy, Surrogafier, ASProxy, Zelune... but all have either perished permanently or the creator
 * has stopped updating them. This proxy script is intended to replace all others.
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

class DefaultController extends Controller
{
    /**
     * @Route("/proxy")
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
                echo render_template($templatePath . 'main.php', array('version' => Proxy::VERSION));
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

                echo render_template($templatePath . 'main.php', array(
                    'url' => $url,
                    'error_msg' => $ex->getMessage(),
                    'version' => Proxy::VERSION
                ));
            }
        }

        exit;
    }

    /**
     * @Route("/proxy/confirm")
     * @Method({"POST"})
     */
    public function postFormDataAction(Request $request)
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

        if(isset($_POST['url'])){

            $url = $_POST['url'];
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
    }
}
