<?php

namespace ApiBundle\Controller\Rest;

use ApiBundle\Controller\Rest\Base\BaseRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;

class SiteCategoryController extends BaseRestController
{
	/**
	 * @Get("/api/{token}/siteCategories/") OR @Get("/api/{token}/siteCategories")
	 * @Get("/api/{token}/sitecategories/") OR @Get("/api/{token}/sitecategories")
	 * @Get("/api/siteCategories/") OR @Get("/api/siteCategories")
	 * @Get("/api/sitecategories/") OR @Get("/api/sitecategories")
	 * @param $token
	 * @return Response
	 */
	public function getAllSiteCategoryNames($token = null)
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
			$this->response['message']['data'] = $this->getDoctrine()->getRepository('AppBundle:Template')->getTemplateNames();
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}
}