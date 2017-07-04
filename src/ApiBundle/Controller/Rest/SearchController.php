<?php

namespace ApiBundle\Controller\Rest;

use ApiBundle\Controller\Rest\Base\BaseRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SearchController extends BaseRestController
{
	/**
	 * Make a general search within the API after a value
	 *
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Make a general search within the API after a value",
	 *  requirements={
	 *      {
	 *          "name"="searchTerm",
	 *          "dataType"="string",
	 *          "requirement"="[a-zA-Z0-9]",
	 *          "description"="Search for the term"
	 *      }
	 *  },
	 * )
	 *
	 * @Get("/api/{token}/search/value/{searchTerm}", name="search_token")
	 * @Get("/api/search/value/{searchTerm}", name="search_logged")
	 * @param null $token
	 * @param $searchTerm
	 * @return Response
	 */
	public function getAttributesContaining($token = null, $searchTerm)
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
			$attributes = $this->getDoctrine()
				->getRepository('AppBundle:Attributes')
				->searchAttributesFor([
					'searchTerm' => $searchTerm
				]);

			$this->response['message'] = $this->addContentData($attributes);
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Make a search after the template element (tag) and value
	 *
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Make a search after the template element (tag) and value",
	 *  requirements={
	 *      {
	 *          "name"="searchByTemplateElement",
	 *          "dataType"="string",
	 *          "requirement"="[a-zA-Z0-9]",
	 *          "description"="Tag name"
	 *      },
	 *     {
	 *          "name"="searchForAttribute",
	 *          "dataType"="string",
	 *          "requirement"="[a-zA-Z0-9]",
	 *          "description"="Search term"
	 *      }
	 *  },
	 * )
	 *
	 * @Get("/api/{token}/search/tag/{searchByTemplateElement}/value/{searchForAttribute}", name="search_token_tag_value")
	 * @Get("/api/search/tag/{searchByTemplateElement}/value/{searchForAttribute}", name="search_logged_tag_value")
	 * @param null $token
	 * @param $searchByTemplateElement
	 * @param $searchForAttribute
	 * @return Response
	 */
	public function getAttributeByTemplateElement($token = null, $searchByTemplateElement, $searchForAttribute)
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
			$attributes = $this->getDoctrine()
				->getRepository('AppBundle:Attributes')
				->searchAttributesFor([
					'searchByTemplateElement' => $searchByTemplateElement,
					'searchTerm'              => $searchForAttribute
				]);

			$this->response['message'] = $this->addContentData($attributes);
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}

	/**
	 * Add related content to the result
	 *
	 * @param $attributes
	 * @return array
	 */
	private function addContentData($attributes)
	{
		$result = [];

		foreach ($attributes as $attrKey => $attribute)
		{
			$contentId = $attribute['contentId'];

			$values = $this->getDoctrine()
				->getRepository('AppBundle:Attributes')
				->findByContentId($contentId);

			foreach ($attribute as $key => $value)
			{
				if ($key == 'contentId')
				{
					$result['data'][$attrKey]['values'] = $values[$contentId];
				}
				else
				{
					$result['data'][$attrKey][$key] = $value;
				}
			}
		}

		if (sizeof($attributes) > 0)
		{
			$result['total_results'] = sizeof($attributes);
		}
		else
		{
			$result['error'] = 'No results found';
		}

		return $result;
	}
}