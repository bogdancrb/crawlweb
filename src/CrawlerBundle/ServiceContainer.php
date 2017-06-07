<?php

namespace CrawlerBundle;

class ServiceContainer
{
	public static $vars = [];
	public static $container;

	public static function initParams($params)
	{
		foreach ($params as $key => $value)
		{
			self::$vars[$key] = $value;
		}
	}

	public static function get($property)
	{
		return isset(self::$vars[$property]) ? self::$vars[$property] : false;
	}

	/**
	 * @return mixed
	 */
	public static function getContainer()
	{
		return self::$container;
	}

	/**
	 * @param mixed $container
	 */
	public static function setContainer($container)
	{
		self::$container = $container;
	}
}