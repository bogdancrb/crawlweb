<?php

namespace CrawlerBundle\Controller\Console;

use CrawlerBundle\Controller\BaseController;
use CrawlerBundle\Controller\Command\CodeReviewVideosCommand;
use CrawlerBundle\Controller\Command\KnpCommand;
use CrawlerBundle\Command\WebCrawlerCommand;
use \Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;

/**
 * Class Application
 */
class Application extends BaseApplication
{
    const VERSION = '1.0';

    public function __construct($kernel)
    {
        parent::__construct($kernel);
    }

    public function getHelp()
    {
        return parent::getHelp();
    }

    /**
     * Initializes all the composer commands
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new WebCrawlerCommand();
        return $commands;
    }
}