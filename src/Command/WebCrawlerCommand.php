<?php

namespace Fetcher\Command;

use Fetcher\WebCrawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WebCrawlerCommand
 * @package Fetcher\Command
 */
class WebCrawlerCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('webcrawler')
			->setDescription('Fetches content')
			->setHelp(<<<EOT
Fetches content
<info>php fetcher fetch</info>
EOT
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$fetcher = new WebCrawler(/*"https://altex.ro/telefoane-tablete-si-gadgets/tablete"*/);

		$fetcher->execute();
	}
}