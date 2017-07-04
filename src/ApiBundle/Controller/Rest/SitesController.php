<?php

namespace ApiBundle\Controller\Rest;

use ApiBundle\Controller\Rest\Base\BaseRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class SitesController extends BaseRestController
{
	/**
	 * @Get("/api/{token}/sites/") OR @Get("/api/{token}/sites")
	 * @Get("/api/sites/") OR @Get("/api/sites")
	 * @param $token
	 * @return Response
	 */
	public function getAllSitesNames($token = null)
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
			$this->response['message']['data'] = $this->getDoctrine()->getRepository('AppBundle:Sites')->getSitesNames();
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}
}