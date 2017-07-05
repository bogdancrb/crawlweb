<?php

namespace ApiBundle\Controller\Rest;

use ApiBundle\Controller\Rest\Base\BaseRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class TagsController extends BaseRestController
{
	/**
	 * Retrieve all available tags
	 *
	 * @Get("/api/tags/", requirements={"url" = ".*\/$"}, methods={"GET"})
	 * @Get("/api/{token}/tags/", requirements={"url" = ".*\/$"}, methods={"GET"})
	 *
	 * @ApiDoc(
	 *  resource=false,
	 *  description="Retrieve all available tags"
	 * )
	 *
	 * @param null $token
	 * @return Response
	 */
	public function getAvailableTemplateElements($token = null)
	{
		if (!$this->checkIfUserLogged($token))
		{
			$this->response = [
				'message' => [
					'error' => 'Please login or use a token key in order to access the API'
				],
				'status_code' => 403
			];
		}
		else
		{
			$this->response['message']['data'] = $this->getDoctrine()
				->getRepository('AppBundle:TemplateElement')
				->getTemplateElementsData();
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Retrieve only tags for requested site name
	 *
	 * @ApiDoc(
	 *  resource=false,
	 *  description="Retrieve only tags for requested site name"
	 * )
	 *
	 * @Get("/api/tags/site/{siteName}")
	 * @Get("/api/{token}/tags/site/{siteName}")
	 *
	 * @param null $token
	 * @param $siteName
	 * @return Response
	 */
	public function getAvailableTemplateElementsForSite($token = null, $siteName)
	{
		if (!$this->checkIfUserLogged($token))
		{
			$this->response = [
				'message' => [
					'error' => 'Please login or use a token key in order to access the API'
				],
				'status_code' => 403
			];
		}
		else
		{
			$this->response['message']['data'] = $this->getDoctrine()
				->getRepository('AppBundle:TemplateElement')
				->getTemplateElementsData([
					'siteName' => $siteName
				]);
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Retrieve only template elements for requested category name
	 *
	 * @ApiDoc(
	 *  resource=false,
	 *  description="Retrieve only template elements for requested category name"
	 * )
	 *
	 * @Get("/api/tags/category/{categoryName}")
	 * @Get("/api/{token}/tags/category/{categoryName}")
	 * @param $categoryName
	 * @return Response
	 */
	public function getAvailableTemplateElementsForCategory($token = null, $categoryName)
	{
		if (!$this->checkIfUserLogged($token))
		{
			$this->response = [
				'message' => [
					'error' => 'Please login or use a token key in order to access the API'
				],
				'status_code' => 403
			];
		}
		else
		{
			$this->response['message']['data'] = $this->getDoctrine()
				->getRepository('AppBundle:TemplateElement')
				->getTemplateElementsData([
					'categoryName' => $categoryName
				]);
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Retrieve only template elements for requested site category name
	 *
	 * @ApiDoc(
	 *  resource=false,
	 *  description="Retrieve only template elements for requested site category name"
	 * )
	 *
	 * @Get("/api/tags/siteCategory/{siteCategoryName}") OR @Get("/api/tags/sitecategory/{siteCategoryName}")
	 * @Get("/api/{token}/tags/siteCategory/{siteCategoryName}") OR @Get("/api/{token}/tags/sitecategory/{siteCategoryName}")
	 * @param $siteCategoryName
	 * @return Response
	 */
	public function getAvailableTemplateElementsForSiteCategory($token = null, $siteCategoryName)
	{
		if (!$this->checkIfUserLogged($token))
		{
			$this->response = [
				'message' => [
					'error' => 'Please login or use a token key in order to access the API'
				],
				'status_code' => 403
			];
		}
		else
		{
			$this->response['message']['data'] = $this->getDoctrine()
				->getRepository('AppBundle:TemplateElement')
				->getTemplateElementsData([
					'siteCategoryName' => $siteCategoryName
				]);
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}
}