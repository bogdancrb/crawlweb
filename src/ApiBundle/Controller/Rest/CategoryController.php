<?php

namespace ApiBundle\Controller\Rest;

use ApiBundle\Controller\Rest\Base\BaseRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CategoryController extends BaseRestController
{
	/**
	 * Get all category names from the database
	 * @Get("/api/{token}/categories/", requirements={"url" = ".*\/$"}, methods={"GET"})
	 * @Get("/api/categories/", requirements={"url" = ".*\/$"}, methods={"GET"})
	 *
	 * @ApiDoc(
	 *  resource=false,
	 *  description="Get all category names from the database"
	 * )
	 *
	 * @param $token
	 * @return Response
	 */
	public function getAllCategoryNames($token = null)
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
			$this->response['message']['data'] = $this->getDoctrine()->getRepository('AppBundle:Category')->getCategoryNames();
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}
}