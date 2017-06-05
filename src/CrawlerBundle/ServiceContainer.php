<?php

namespace CrawlerBundle;

class ServiceContainer
{
	public static $vars = [];

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
}