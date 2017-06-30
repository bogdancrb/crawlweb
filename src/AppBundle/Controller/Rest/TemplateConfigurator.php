<?php

namespace AppBundle\Controller\Rest;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Category;
use AppBundle\Entity\Content;
use AppBundle\Entity\Sites;
use AppBundle\Entity\Template;
use AppBundle\Entity\TemplateElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateConfigurator extends BaseRestController
{
	/**
	 * @Route("/template/data", name="post_template_data")
	 * @Method({"POST"})
	 */
	public function postTemplateDataAction(Request $request)
	{
		$proxyData = $request->request->get('proxyData');
		$elements = json_decode($proxyData['elements']);

		$category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByName($proxyData['category_name']);

		$connection = $this->getEntityManager()->getConnection();
		$connection->beginTransaction();

		try
		{
			if (count($category) <= 0)
			{
				$category = new Category();
				$category->setName($proxyData['category_name']);
			}

			$site = $this->getDoctrine()->getRepository('AppBundle:Sites')->findOneByName($proxyData['site_name']);

			if (count($site) <= 0)
			{
				$site = new Sites();
				$site->setName($proxyData['site_name'])
					->setMainUrl($proxyData['site_main_url'])
					->setCategory($category);
				$this->getEntityManager()->persist($site);
			}

			$content = $this->getDoctrine()->getRepository('AppBundle:Content')->findOneByUrl($proxyData['content_url']);

			if (count($content) <= 0)
			{
				$content = new Content();
				$content->setUrl($proxyData['content_url'])
					->setSites($site);
				$this->getEntityManager()->persist($content);
			}

			$template = $this->getDoctrine()->getRepository('AppBundle:Template')->findOneBy([
				'name'  => $proxyData['template_name'],
				'sites' => $site
			]);

			if (count($template) <= 0)
			{
				$template = new Template();
				$template->setName($proxyData['template_name'])
					->setSites($site);
				$this->getEntityManager()->persist($template);
			}

			foreach ($elements as $element)
			{
				$templateElement = $this->getDoctrine()->getRepository('AppBundle:TemplateElement')->findOneBy([
					'name'     => $element->name,
					'template' => $template
				]);

				if (count($templateElement) <= 0)
				{
					$templateElement = new TemplateElement();
					$templateElement->setName($element->name);
				}

				$templateElement->setCssPath($element->css_path)
					->setTemplate($template)
					->setIgnoreAttributeValue(
						$element->url_only == "yes"
							? TemplateElement::IGNORE_ATTRIBUTE_VALUE_YES
							: TemplateElement::IGNORE_ATTRIBUTE_VALUE_NO);
				$this->getEntityManager()->persist($templateElement);
			}

			$this->getEntityManager()->persist($category);
			$this->getEntityManager()->flush();

			$connection->commit();

			$message = 'Data was successfully saved !';
		}
		catch(Exception $e)
		{
			$message = 'Error occurred while processing request';

			$connection->rollBack();
		}

		return new Response(
			$message,
			'200',
			['Content-Type' => 'text/plain']
		);
	}

	/**
	 * @Route("/template/search/all", name="get_template_search_all")
	 * @Method({"GET"})
	 */
	public function getAllSearchDataAction()
	{
		$data = [
			'category_names' 			=> $this->getDoctrine()->getRepository('AppBundle:Category')->getCategoryNames(),
			'sites_names'				=> $this->getDoctrine()->getRepository('AppBundle:Sites')->getSitesNames(),
			'template_names'			=> $this->getDoctrine()->getRepository('AppBundle:Template')->getTemplateNames(),
			'template_element_names'	=> $this->getDoctrine()->getRepository('AppBundle:TemplateElement')->getTemplateElementNames()
		];

		return new Response(
			json_encode($data),
			'200',
			['Content-Type' => 'application/json']
		);
	}
}