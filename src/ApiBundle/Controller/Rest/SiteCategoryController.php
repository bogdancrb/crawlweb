<?php

namespace ApiBundle\Controller\Rest;

use ApiBundle\Controller\Rest\Base\BaseRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SiteCategoryController extends BaseRestController
{
	/**
	 * Get all the site categories available within the system
	 *
	 * @ApiDoc(
	 *  resource=false,
	 *  description="Get all the site categories available within the system",
	 * )
	 *
	 * @Get("/api/{token}/siteCategories/", requirements={"url" = ".*\/$"}, methods={"GET"})
	 * @Get("/api/{token}/sitecategories/", requirements={"url" = ".*\/$"}, methods={"GET"})
	 * @Get("/api/siteCategories/", requirements={"url" = ".*\/$"}, methods={"GET"})
	 * @Get("/api/sitecategories/", requirements={"url" = ".*\/$"}, methods={"GET"})
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