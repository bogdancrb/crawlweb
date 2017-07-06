<?php

namespace CrawlerBundle\Controller;

use AppBundle\Entity\Attributes;
use AppBundle\Entity\Content;
use AppBundle\Entity\Template;
use AppBundle\Entity\TemplateElement;
use CrawlerBundle\ServiceContainer;
use DateTime;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

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
	/** @var AbstractManagerRegistry  */
	private $doctrine;

	/** @var Container  */
	private $container;

	private $foundTemplate = false;
	private $outdatedTemplate = false;
	private $possibleOutdatedTemplate = false;
	private $sendOutdatedMessage = false;

	private $crawler;

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
		$this->client->setHeader('User-Agent','Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');

		$this->container = ServiceContainer::getContainer();
		$this->doctrine = ServiceContainer::get('doctrine');
		$this->entityManager = $this->doctrine->getEntityManager();
		$this->currentTemplateElementIds = [];
	}

	/**
	 *
	 */
	public function executeCrawler()
	{
		$sleepSecondsConfig = $this->container->getParameter('crawler.sleep_seconds');
		$timeIntervalConfig = $this->container->getParameter('crawler.time_interval');
		$proxyIps = $this->container->hasParameter('crawler.proxy_ips');

		if (!empty($proxyIps))
		{
			$proxyIps = $this->container->getParameter('crawler.proxy_ips');
		}

		$crawlContent = $this->doctrine
			->getRepository('AppBundle:Content')
			->retrieveContent($timeIntervalConfig);

		/** @var Content $content */
		foreach ($crawlContent as $content)
		{
			if (!empty($proxyIps))
			{
				$proxyIp = $this->getProxyIpAndAgent($proxyIps);

				var_dump($proxyIp);

				$this->client->getClient()->setDefaultOption('config/curl/' . CURLOPT_PROXY, 'http://' . $proxyIp['IP'] . ':80');

				$this->client->setHeader('User-Agent', $proxyIp['Agent']);
			}

			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			try
			{
				$this->foundTemplate = false;
				$this->outdatedTemplate = false;
				$this->possibleOutdatedTemplate = false;
				$this->sendOutdatedMessage = false;

				$this->extractData($content);

				var_dump($this->possibleOutdatedTemplate);

				if (!$this->foundTemplate)
				{
					$this->sendMailTemplateNotFound($content);

					throw new \Exception('no template found');
				}
				else if ($this->outdatedTemplate)
				{
					if ($this->sendOutdatedMessage)
					{
						$this->sendMailTemplateOutdated($template);
					}

					throw new \Exception('template outdated, please update');
				}
				else if ($this->possibleOutdatedTemplate)
				{
					if ($this->sendOutdatedMessage)
					{
						var_dump('template is possible to be outdated, please check the template settings');
					}
				}

				$lastAccessedDate = new DateTime();
				$lastAccessedDate->format("Y-m-d H:i:s");

				$content->setLastAccessed($lastAccessedDate);

				$this->entityManager->persist($content);
				$this->entityManager->flush();

				$connection->commit();
			}
			catch (\Exception $e)
			{
				var_dump($e->getMessage());

				$connection->rollBack();
			}

			if (!empty($sleepSecondsConfig))
			{
				var_dump('Process halted for '. $sleepSecondsConfig/60 . ' minutes');

				 // Halt the crawling process, so that we do not get banned from the website :)
				sleep($sleepSecondsConfig);
			}
		}
	}

	private function getProxyIpAndAgent($proxyIps = [])
	{
		srand();
		$randIndex = rand(1, sizeof($proxyIps) - 1);

		return $proxyIps[$randIndex];
	}

	/**
	 * Extract all the needed data
	 *
	 * @param Content $content
	 */
	private function extractData($content)
	{
		var_dump($content->getUrl());

		$this->crawler = $this->client->request('GET', $content->getUrl());

		if ($content->getAttributes()->count() > 0)
		{
			$this->deleteOldValuesForContent($content);
		}

		$templates = $content->getSites()->getTemplate();

		/** @var Template $template */
		foreach ($templates as $template)
		{
			$this->addNewValues($content, $template);

			if ($this->foundTemplate)
			{
				break;
			}
		}
	}

	/**
	 * Delete old values from the database
	 *
	 * @param Content $content
	 */
	private function deleteOldValuesForContent($content)
	{
		$attributes = $content->getAttributes();

		/** @var Attributes $attribute */
		foreach ($attributes as $attribute)
		{
			var_dump('[OLD DATA]: ' . $attribute->getTemplateElement()->getName()
				. ': ' . strip_tags(fread($attribute->getValue(), 1000000)));

			$this->entityManager->remove($attribute);
		}
	}

	/**
	 * Add the new values to the database
	 *
	 * @param Content $content
	 * @param Template $template
	 * @return bool
	 */
	private function addNewValues($content, $template)
	{
		var_dump($template->getName());

		$templateElements = $template->getTemplateElement();

		$this->outdatedTemplate = false;
		$attribute = null;
		$countSuccessfullyFetchedElements = 0;

		/** @var TemplateElement $templateElement */
		foreach ($templateElements as $templateElement)
		{
			$extractedValues = $this->crawler->filter($templateElement->getCssPath());
			$countSuccessfullyFetchedElements += count($extractedValues);

			if (count($extractedValues) > 0)
			{
				/** @var \DOMElement $value */
				foreach ($extractedValues as $value)
				{
					$this->foundTemplate = true;

					$attributeValue = trim($value->nodeValue);
					$url = strstr($value->getAttribute('href'), 'javascript') == false
						? $value->getAttribute('href')
						: null;

					if (!empty($attributeValue) && !$templateElement->getIgnoreAttributeValue())
					{
						var_dump('[NEW DATA]: ' . $templateElement->getName() . ': ' . $attributeValue);

						$attribute = new Attributes();
						$attribute->setValue($attributeValue)
							->setContent($content)
							->setTemplateElement($templateElement);
						$this->entityManager->persist($attribute);
					}

					if (!empty($url))
					{
						if (strstr($url, 'http://') == false)
						{
							$url = $content->getSites()->getMainUrl() . $url;
						}

						var_dump('HREF FOUND: ' . $url);

						/** @var Content $contentUrlFound */
						$contentUrlFound = $this->doctrine
							->getRepository('AppBundle:Content')
							->findOneByUrl($url);

						if (count($contentUrlFound) <= 0)
						{
							$newContent = new Content();
							$newContent->setUrl($url)
								->setSites($content->getSites());
							$this->entityManager->persist($newContent);
						}
					}
				}
			}
			else
			{
				$this->outdatedTemplate = true;
			}
		}

		if ($countSuccessfullyFetchedElements > 2)
		{
			$this->possibleOutdatedTemplate = true;
			$this->outdatedTemplate = false;
		}

		if ($this->outdatedTemplate || $this->possibleOutdatedTemplate)
		{
			$this->setTemplateOutdated($template);
		}

		if (isset($attribute) && !$this->foundTemplate)
		{
			$this->entityManager->remove($attribute);
			$attribute = null;
		}
	}

	/**
	 * Set template as outdated
	 *
	 * @param Template $template
	 */
	private function setTemplateOutdated($template)
	{
		$templateOutdatedLastNotified = $template->getOutdatedLastNotified();

		if (empty($templateOutdatedLastNotified))
		{
			$lastNotifiedDate = new DateTime();
			$lastNotifiedDate->format("Y-m-d H:i:s");

			$template->setOutdatedLastNotified($lastNotifiedDate);

			$this->sendOutdatedMessage = true;
		}
		else
		{
			$templateNotifyOutdatedConfig = $this->container->getParameter('crawler.template_notify_outdated');

			$outdatedDateAlert = strtotime('+ '
				. $templateNotifyOutdatedConfig
				. ' day', $templateOutdatedLastNotified->getTimestamp());

			if ($outdatedDateAlert <= strtotime(date("Y-m-d H:i:s")))
			{
				$lastNotifiedDate = new DateTime();
				$lastNotifiedDate->format("Y-m-d H:i:s");

				$template->setOutdatedLastNotified($lastNotifiedDate);

				$this->sendOutdatedMessage = true;
			}
		}
	}

	/**
	 * @param Template $template
	 * @throws \Exception
	 */
	private function sendMailTemplateOutdated($template)
	{
		$message = \Swift_Message::newInstance()
			->setSubject('Template outdated')
			->setFrom('admin@crawlweb.info')
			->setBody(
				$this->renderView(
					'email/template_outdated.html.twig',
					array('id' => $template->getId())
				)
			)
		;
		$this->container->get('mailer')->send($message);
	}

	/**
	 * @param Content $content
	 * @throws \Exception
	 */
	private function sendMailTemplateNotFound($content)
	{
		$message = \Swift_Message::newInstance()
			->setSubject('Template not found')
			->setFrom('admin@crawlweb.info')
			->setBody(
				$this->renderView(
					'email/template_not_found.html.twig',
					array('url' => $content->getId())
				)
			)
		;
		$this->container->get('mailer')->send($message);
	}

	protected function renderView($view, array $parameters = array(), Response $response = null)
	{
		if ($this->container->has('templating')) {
			return $this->container->get('templating')->renderResponse($view, $parameters, $response);
		}

		if (!$this->container->has('twig')) {
			throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available.');
		}

		if (null === $response) {
			$response = new Response();
		}

		$response->setContent($this->container->get('twig')->render($view, $parameters));

		return $response;
	}
}