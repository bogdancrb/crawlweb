<?php

namespace CrawlerBundle\Command;

use CrawlerBundle\Controller\WebCrawler;
use CrawlerBundle\ServiceContainer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Tests\Service;

/**
 * Class WebCrawlerCommand
 * @package CrawlerBundle\Command
 */
class WebCrawlerCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('crawler:init')
			->setDescription('Fetches content')
			->setHelp(<<<EOT
Starts the web crawler
<info>php fetcher.php webcrawler</info>
EOT
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->initServiceContainer();

		$fetcher = new WebCrawler();

		$fetcher->executeCrawler();
	}

	private function initServiceContainer()
	{
		ServiceContainer::setContainer($this->getContainer());

		ServiceContainer::initParams([
			'doctrine' => $this->getContainer()->get('doctrine')
		]);
	}
}