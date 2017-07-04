<?php

namespace AppBundle\EventListener;

use AppBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class MyMenuItemListListener
{
	public function onSetupMenu(SidebarMenuEvent $event)
	{
		$request = $event->getRequest();
		$items = $this->getMenu($request);

		foreach ($items as $item) {
			$event->addItem($item);
		}
	}

	protected function getMenu(Request $request)
	{
		// Build your menu here by constructing a MenuItemModel array
		$menuItems = array(
			$overviewPage = new MenuItemModel('ItemId1', 'Overview', 'menu_route_overview'),
			$crawlerConfiguratorPage = new MenuItemModel('ItemId2', 'Configure Crawler'),
			$usersPage = new MenuItemModel('ItemId3', 'Users', 'menu_route_users'),
		);

		// Add some children

		$crawlerConfiguratorPage->addChild(new MenuItemModel('ChildOneItemId1', 'Inspector', 'menu_route_proxy', array(), 'fa fa-rss-square'));
		$crawlerConfiguratorPage->addChild(new MenuItemModel('ChildTwoItemId2', 'Categories', 'menu_route_categories'));
		$crawlerConfiguratorPage->addChild(new MenuItemModel('ChildTwoItemId3', 'Sites', 'menu_route_sites'));
		$crawlerConfiguratorPage->addChild(new MenuItemModel('ChildTwoItemId4', 'Templates', 'menu_route_templates'));
		$crawlerConfiguratorPage->addChild(new MenuItemModel('ChildTwoItemId5', 'Template elements', 'menu_route_template_elements'));
		$crawlerConfiguratorPage->addChild(new MenuItemModel('ChildTwoItemId6', 'Content', 'menu_route_content'));

		return $this->activateByRoute($request->get('_route'), $menuItems);
	}

	protected function activateByRoute($route, $items)
	{
		foreach($items as $item) {
			if($item->hasChildren()) {
				$this->activateByRoute($route, $item->getChildren());
			}
			else {
				if($item->getRoute() == $route) {
					$item->setIsActive(true);
				}
			}
		}

		return $items;
	}
}