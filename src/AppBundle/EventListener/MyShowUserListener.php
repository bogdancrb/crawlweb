<?php

namespace AppBundle\EventListener;

use Avanzu\AdminThemeBundle\Event\ShowUserEvent;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MyShowUserListener
{
	public $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function onShowUser(ShowUserEvent $event) 
	{
		$user = $this->getUser();
		$event->setUser($user);
	}

	protected function getUser() 
	{
		/** @var User $user */
		$user = $this->container->get('security.token_storage')->getToken()->getUser();

		return $user;
	}
}