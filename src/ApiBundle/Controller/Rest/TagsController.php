<?php

namespace ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class TagsController extends FOSRestController
{
	/**
	 * Retrieve all template elements (a.k.a. tags) available
	 *
	 * @Get("/api/tags") OR @Get("/api/tags/")
	 * @return Response
	 */
	public function getAvailableTemplateElements()
	{
		$templateElements = $this->getDoctrine()
			->getRepository('AppBundle:TemplateElement')
			->getTemplateElementsData();

		return new Response(json_encode($templateElements),
			'200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Retrieve only template elements for requested site name
	 *
	 * @Get("/api/tags/site/{siteName}")
	 * @param $siteName
	 * @return Response
	 */
	public function getAvailableTemplateElementsForSite($siteName)
	{
		$templateElements = $this->getDoctrine()
			->getRepository('AppBundle:TemplateElement')
			->getTemplateElementsData([
				'siteName' => $siteName
			]);

		return new Response(
			json_encode($templateElements),
			'200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Retrieve only template elements for requested category name
	 *
	 * @Get("/api/tags/category/{categoryName}")
	 * @param $categoryName
	 * @return Response
	 */
	public function getAvailableTemplateElementsForCategory($categoryName)
	{
		$templateElements = $this->getDoctrine()
			->getRepository('AppBundle:TemplateElement')
			->getTemplateElementsData([
				'categoryName' => $categoryName
			]);

		return new Response(
			json_encode($templateElements),
			'200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Retrieve only template elements for requested site category name
	 *
	 * @Get("/api/tags/siteCategory/{siteCategoryName}") OR @Get("/api/tags/sitecategory/{siteCategoryName}")
	 * @param $siteCategoryName
	 * @return Response
	 */
	public function getAvailableTemplateElementsForSiteCategory($siteCategoryName)
	{
		$templateElements = $this->getDoctrine()
			->getRepository('AppBundle:TemplateElement')
			->getTemplateElementsData([
				'siteCategoryName' => $siteCategoryName
			]);

		return new Response(
			json_encode($templateElements),
			'200',
			['Content-Type' => 'application/json']
		);
	}
}