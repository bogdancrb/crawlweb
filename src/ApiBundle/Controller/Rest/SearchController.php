<?php

namespace ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends FOSRestController
{
	/**
	 * Make a general search within the API after a value
	 *
	 * @Get("/api/search/{searchTerm}")
	 * @param $searchTerm
	 * @return Response
	 */
	public function getAttributesContaining($searchTerm)
	{
		$attributes = $this->getDoctrine()
			->getRepository('AppBundle:Attributes')
			->searchAttributesFor([
				'searchTerm' => $searchTerm
			]);

		$result = $this->getAddContentData($attributes);

		return new Response(
			json_encode($result), 
			'200', 
			['Content-Type' => 'application/json']
		);
	}

	public function getAllSitesNames()
	{

	}

	/**
	 * @Get("/api/search/tag/{searchByTemplateElement}/value/{searchForAttribute}")
	 * @param $searchByTemplateElement
	 * @param $searchForAttribute
	 * @return Response
	 */
	public function getAttributeByTemplateElement($searchByTemplateElement, $searchForAttribute)
	{
		$attributes = $this->getDoctrine()
			->getRepository('AppBundle:Attributes')
			->searchAttributesFor([
				'searchByTemplateElement' => $searchByTemplateElement,
				'searchTerm' => $searchForAttribute
			]);

		$result = $this->getAddContentData($attributes);

		return new Response(
			json_encode($result),
			'200',
			['Content-Type' => 'application/json']
		);
	}

	private function getAddContentData($attributes)
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
					$result[$attrKey]['values'] = $values[$contentId];
				}
				else
				{
					$result[$attrKey][$key] = $value;
				}
			}
		}

		return $result;
	}
}