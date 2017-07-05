<?php

namespace AppBundle\Controller;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
	const VIEWS_FOLDER_NAME = 'admin';

	/**
	 * @Route("/admin", name="adminpage")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction()
	{
		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'index.html.twig');
	}

	/**
	 * @Route("/admin/categories", name="adminc_ategories")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewCategoriesAction()
	{
		$results = $this->getDoctrine()->getRepository('AppBundle:Category')->getCategoriesWithSitesTotals();

		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'categories.html.twig', [
				'results' => $results
			]
		);
	}

	/**
	 * @Route("/admin/sites", name="adminc_sites")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewSitesAction()
	{
		$results = $this->getDoctrine()->getRepository('AppBundle:Sites')->getSitesWithTotalContent();

		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'sites.html.twig', [
			'results' => $results
			]
		);
	}

	/**
	 * @Route("/admin/templates", name="admin_templates")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewTemplatesAction()
	{
		$results = $this->getDoctrine()->getRepository('AppBundle:Template')->getTemplatesAndTotalTemplateElements();

		foreach ($results as $key => $result)
		{
			/** @var DateTime $lastNotifiedDate */
			$lastNotifiedDate = $result['outdatedLastNotified'];

			$results[$key]['outdatedLastNotified'] = !empty($lastNotifiedDate) ? $lastNotifiedDate->format("d-m-Y H:i:s") : '';
		}

		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'templates.html.twig', [
				'results' => $results
			]
		);
	}

	/**
	 * @Route("/admin/templates/elements", name="admin_templates_elements")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewTemplateElementsAction()
	{
		$results = $this->getDoctrine()->getRepository('AppBundle:TemplateElement')->getTemplateElements();

		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'template_elements.html.twig', [
				'results' => $results
			]
		);
	}

	/**
	 * @Route("/admin/content", name="admin_content")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewContentAction()
	{
		$results = $this->getDoctrine()->getRepository('AppBundle:Content')->getContent();

		foreach ($results as $key => $result)
		{
			/** @var DateTime $lastNotifiedDate */
			$lastAccessedDate = $result['lastAccessed'];

			$results[$key]['lastAccessed'] = !empty($lastAccessedDate) ? $lastAccessedDate->format("d-m-Y H:i:s") : '';
		}

		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'content.html.twig', [
			'results' => $results
			]
		);
	}

	/**
	 * @Route("/admin/users", name="admin_users")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewUsersAction()
	{
		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'users.html.twig');
	}
}