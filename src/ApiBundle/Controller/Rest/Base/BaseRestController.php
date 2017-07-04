<?php

namespace ApiBundle\Controller\Rest\Base;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\FOSRestBundle;
use FOS\UserBundle\Doctrine\UserManager;

class BaseRestController extends FOSRestController
{
	private $isUserLogged;
	private $user;
	private $entityManager;
	protected $response;

	public function checkIfUserLogged($token = null)
	{
		$valid = true;

		if (empty($token) && !$this->getIsUserLogged())
		{
			$valid = false;
		}

		if (!empty($token) && !$this->getUserForToken($token))
		{
			$valid = false;
		}

		return $valid;
	}

	/**
	 * @return User
	 */
	public function getUserForToken($token)
	{
		if (empty($this->user))
		{
			/** @var UserManager $userManager */
			$userManager = $this->container->get('fos_user.user_manager');

			$this->user = $userManager->findUserBy([
				'apiToken' => $token
			]);
		}

		return $this->user;
	}

	/**
	 * @return mixed
	 */
	private function getIsUserLogged()
	{
		if (empty($this->isUserLogged))
		{
			$this->isUserLogged = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');
		}

		return $this->isUserLogged;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		if (empty($this->entityManager))
		{
			$this->entityManager = $this->getDoctrine()->getManager();
		}

		return $this->entityManager;
	}

}