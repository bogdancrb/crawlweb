<?php

namespace CrawlerBundle\Controller;

use AppBundle\Entity\Attributes;
use AppBundle\Entity\Content;
use AppBundle\Entity\Template;
use AppBundle\Entity\TemplateElement;
use CrawlerBundle\ServiceContainer;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class WebCrawler
 * @package CrawlerBundle\Controller
 */
class WebCrawler
{
	private $client;
	private $currentTemplateElementIds;

	/** @var  EntityManager */
	private $entityManager;

	/** @var Container  */
	private $container;

	/**
	 * WebCrawler constructor.
	 */
	public function __construct()
	{
		$this->client = new Client();
		$this->client->getClient()->setDefaultOption('verify', 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_TIMEOUT, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_CONNECTTIMEOUT, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYHOST, 0);
		$this->client->getClient()->setDefaultOption('config/curl/'.CURLOPT_SSL_VERIFYPEER, 0);
		$this->client->setHeader('User-Agent','Google Bot: Googlebot/2.1');

		$this->container = ServiceContainer::getContainer();
		$this->entityManager = ServiceContainer::get('doctrine')->getEntityManager();
		$this->currentTemplateElementIds = [];
	}

	/**
	 *
	 */
	public function executeCrawler()
	{
		$crawlContent = $this->entityManager->getRepository('AppBundle:Content')->retrieveContent(
			$this->container->getParameter('crawler.time_interval')
		);

		/** @var Content $content */
		foreach ($crawlContent as $content)
		{
			var_dump("","","","");

			$foundTemplate = false;
			$outdatedTemplate = false;

			var_dump($content->getUrl());
			$crawler = $this->client->request('GET', $content->getUrl());

			if ($content->getAttributes()->count() > 0)
			{
				$templateElementIds = [];
				$templateId = 0;

				/** @var Attributes $attribute */
				foreach ($content->getAttributes() as $attribute)
				{
					$templateId = $attribute->getTemplateElement()->getTemplate()->getId();

					$templateElementIds[] = $attribute->getTemplateElement()->getId();

					var_dump('[OLD DATA]: ' . $attribute->getTemplateElement()->getName()
						. ': ' . strip_tags(fread($attribute->getValue(), 10000)));
					var_dump($attribute->getTemplateElement()->getCssPath());

					$extractedValue = $crawler->filter($attribute->getTemplateElement()->getCssPath())->text();

					var_dump('[NEW DATA]: ' . $attribute->getTemplateElement()->getName() . ': ' . trim($extractedValue));

					var_dump("######################################");
				}

				$currentTemplateElementIds = $this->entityManager->getRepository('AppBundle:TemplateElement')->findByTemplateId($templateId);

				array_walk($currentTemplateElementIds, function($elem){
					$this->currentTemplateElementIds[] = $elem['id'];
				});

				$newTemplateElementIds = array_diff($this->currentTemplateElementIds, $templateElementIds);

				if (!empty($newTemplateElementIds))
				{
					$newTemplateElements = $this->entityManager->getRepository('AppBundle:TemplateElement')->findByIds($newTemplateElementIds);

					/** @var TemplateElement $templateElement */
					foreach ($newTemplateElements as $templateElement)
					{
						$extractedValues = $crawler->filter($templateElement->getCssPath());

						if (count($extractedValues) > 0)
						{
							/** @var \DOMElement $value */
							foreach ($extractedValues as $value)
							{
								$foundTemplate = true;

								var_dump('[NEW DATA]: ' . $templateElement->getName() . ': ' . trim($value->nodeValue));

								if (!empty($value->getAttribute('href')))
								{
									var_dump('HREF FOUND: ' . $value->getAttribute('href'));
								}
							}
						}
					}

					if (!$foundTemplate)
					{
						// TODO log error and mail
						// TODO mark content as failed in DB

						var_dump('[1] no template found');
					}
				}
			}
			else
			{
				$templates = $content->getSites()->getTemplate();

				/** @var Template $template */
				foreach ($templates as $template)
				{
					$outdatedTemplate = false;
					
					$templateElements = $template->getTemplateElement();

					var_dump($template->getName());

					/** @var TemplateElement $templateElement */
					foreach ($templateElements as $templateElement)
					{
						$extractedValues = $crawler->filter($templateElement->getCssPath());

						if (count($extractedValues) > 0)
						{
							/** @var \DOMElement $value */
							foreach ($extractedValues as $value)
							{
								$foundTemplate = true;

								if (!empty($value->nodeValue))
								{
									var_dump('[NEW DATA]: ' . $templateElement->getName() . ': ' . trim($value->nodeValue));
								}

								if (!empty($value->getAttribute('href')))
								{
									var_dump('HREF FOUND: ' . $value->getAttribute('href'));
								}
							}
						}
						else
						{
							$outdatedTemplate = true;
						}
					}

					if ($foundTemplate)
					{
						break;
					}
				}

				if (!$foundTemplate)
				{
					// TODO log error and mail
					// TODO mark content as failed in DB

					var_dump('[2] no template found');
				}
				else if ($outdatedTemplate)
				{
					// TODO log error and mail
					// TODO mark content as outdated in DB
					var_dump('template outdated');
				}
			}

			//sleep(5); // add delay
		}
	}
}