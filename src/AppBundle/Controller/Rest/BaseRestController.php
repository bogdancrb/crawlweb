<?php

namespace AppBundle\Controller\Rest;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseRestController extends Controller
{
	/** @var EntityManager */
	private $entityManager;

	public function getEntityManager()
	{
		if ($this->entityManager == null)
		{
			$this->entityManager = $this->get('doctrine')->getManager();
		}

		return $this->entityManager;
	}
}