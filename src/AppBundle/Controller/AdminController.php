<?php

namespace AppBundle\Controller;

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
		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'categories.html.twig');
	}

	/**
	 * @Route("/admin/sites", name="adminc_sites")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewSitesAction()
	{
		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'sites.html.twig');
	}

	/**
	 * @Route("/admin/templates", name="admin_templates")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewTemplatesAction()
	{
		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'templates.html.twig');
	}

	/**
	 * @Route("/admin/templates/elements", name="admin_templates_elements")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewTemplateElementsAction()
	{
		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'template_elements.html.twig');
	}

	/**
	 * @Route("/admin/content", name="admin_content")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewContentAction()
	{
		return $this->render(self::VIEWS_FOLDER_NAME . DIRECTORY_SEPARATOR . 'content.html.twig');
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