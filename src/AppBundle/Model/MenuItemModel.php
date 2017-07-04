<?php

namespace AppBundle\Model;

use Avanzu\AdminThemeBundle\Model\MenuItemInterface as ThemeMenuItem;

class MenuItemModel implements ThemeMenuItem
{
	public $id;
	public $label;
	public $route;
	public $isActive;
	public $children;
	public $parent;
	public $classes;
	public $routeArgs;
	public $options;

	// 'ItemId', 'ItemDisplayName', 'item_symfony_route', array(/* options */), 'iconclasses fa fa-plane'
	public function __construct($itemId = null, $itemDisplayName = null, $itemSymfonyRoute = null, $options = [], $classes = null)
	{
		$this->id = $itemId;
		$this->label = $itemDisplayName;
		$this->route = $itemSymfonyRoute;
		$this->options = $options;
		$this->classes = $classes;
		$this->routeArgs = [];
	}

	/**
	 * @return mixed
	 */
	public function getIdentifier()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getLabel()
	{
		// TODO: Implement getLabel() method.
	}

	/**
	 * @return mixed
	 */
	public function getRoute()
	{
		return !empty($this->route) ? $this->route : '';
	}

	/**
	 * @return mixed
	 */
	public function isActive()
	{
		return $this->isActive;
	}

	/**
	 * @param $isActive
	 *
	 * @return mixed
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
	}

	/**
	 * @return mixed
	 */
	public function hasChildren()
	{
		return (!empty($this->children) && sizeof($this->children) > 0);
	}

	/**
	 * @return mixed
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * @param ThemeMenuItem $child
	 *
	 * @return mixed
	 */
	public function addChild(ThemeMenuItem $child)
	{
		$this->children[] = $child;
	}

	/**
	 * @param ThemeMenuItem $child
	 *
	 * @return mixed
	 */
	public function removeChild(ThemeMenuItem $child)
	{
		// TODO: Implement removeChild() method.
	}

	/**
	 * @return mixed
	 */
	public function getIcon()
	{
		// TODO: Implement getIcon() method.
	}

	/**
	 * @return mixed
	 */
	public function getBadge()
	{
		// TODO: Implement getBadge() method.
	}

	/**
	 * @return mixed
	 */
	public function getBadgeColor()
	{
		// TODO: Implement getBadgeColor() method.
	}

	/**
	 * @return mixed
	 */
	public function getParent()
	{
		// TODO: Implement getParent() method.
	}

	/**
	 * @return mixed
	 */
	public function hasParent()
	{
		// TODO: Implement hasParent() method.
	}

	/**
	 * @param ThemeMenuItem $parent
	 *
	 * @return mixed
	 */
	public function setParent(ThemeMenuItem $parent = null)
	{
		// TODO: Implement setParent() method.
	}

	/**
	 * @return ThemeMenuItem|null
	 */
	public function getActiveChild()
	{
		// TODO: Implement getActiveChild() method.
	}

	/**
	 * @return null
	 */
	public function getRouteArgs()
	{
		return $this->routeArgs;
	}
}