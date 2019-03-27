<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WarmHttpCacheCommand
 *
 * @package WndevDeveloper\Commands
 */
class WarmHttpCacheCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:warm:http:cache')
            ->setDescription('Warm up http cache')
            ->addArgument(
                'url',
                InputArgument::OPTIONAL,
                'The URL to warm up',
                'http://local.flamme.de/detail/index/sArticle/79728'
            )
            ->setHelp('The <info>%command.name%</info> warms up the http cache')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        /** @var \Shopware\Components\HttpCache\CacheWarmer $cacheWarmer */
        $cacheWarmer = $this->container->get('http_cache_warmer');
        $cacheWarmer->callUrls([$url], 1);

        $output->writeln("\n The HttpCache is now warmed up");
    }
}
