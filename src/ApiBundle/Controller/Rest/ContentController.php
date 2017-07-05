<?php

namespace ApiBundle\Controller\Rest;

use ApiBundle\Controller\Rest\Base\BaseRestController;
use AppBundle\Entity\Content;
use AppBundle\Entity\Sites;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ContentController extends BaseRestController
{
	/**
	 * Request new content to be added into the database for crawling
	 *
	 * @Route("/api/{token}/add/content", methods={"PUT"})
	 * @Route("/api/add/content", methods={"PUT"})
	 * @RequestParam(
	 *     name="url",
	 *     nullable=false,
	 *     allowBlank=false,
	 *     strict=true,
	 * )
	 *
	 * @ApiDoc(
	 *  resource=false,
	 *  description="Request new content to be added into the database for crawling",
	 *  requirements={
	 *     {
	 *     		"name": "url",
	 *     		"dataType"="string",
	 *          "requirement"="[a-zA-Z0-9]",
	 *          "description"="The url including the http:// protocol"
	 *
	 *     }
	 * 	 }
	 * )
	 *
	 * @param null $token
	 * @param ParamFetcher $paramFetcher
	 * @return Response
	 */
	public function putContent($token = null, ParamFetcher $paramFetcher)
	{
		$paramFetcher->all();
		$contentUrl = $paramFetcher->get('url');

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
			$match = null;

			if (!preg_match_all('/[http]+[s]*[:\/\/]+[www.]*[a-zA-Z0-9]+[.]+[a-zA-Z0-9]+[.]*[a-zA-Z]*' .
				'|[www.]+[a-zA-Z0-9]+[.]+[a-zA-Z0-9]+[.]+[a-zA-Z]+/isX', $contentUrl, $match))
			{
				$this->response = [
					'message' => [
						'error' => 'Please provide a valid content URL'
					],
				];
			}
			else
			{
				/** @var Content $content */
				$content = $this->getDoctrine()->getRepository('AppBundle:Content')->findOneByContentUrl($contentUrl);

				if (empty($content))
				{
					/** @var Sites $site */
					$site = $this->getDoctrine()->getRepository('AppBundle:Sites')->findOneByMainUrl($match[0]);

					if (empty($site))
					{
						$this->response['message']['data'] = [
							'error' => 'The main url of the content is not mapped within the application'
						];
					}
					else
					{
						$content = new Content();
						$content->setUrl($contentUrl)
							->setSites($site);
						$this->getEntityManager()->persist($content);
						$this->getEntityManager()->flush();

						$this->response['message']['data'] = [
							'success' => 'The content was added within the system and will be reviewed'
						];
					}
				}
				else
				{
					$this->response['message']['data'] = [
						'error' => 'The provided content URL already exists within the system'
					];
				}
			}
		}

		return new Response(
			json_encode($this->response['message']),
			!empty($this->response['status_code']) ? $this->response['status_code'] : '200',
			['Content-Type' => 'application/json']
		);
	}
}