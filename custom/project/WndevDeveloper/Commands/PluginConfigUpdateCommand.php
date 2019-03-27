<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WndevDeveloper\Components\PluginConfig;

class PluginConfigUpdateCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:pluginconfig:update')
            ->setDescription('Save current config of plugins to file for later retrieval.')
            ->addOption('plugin', null, InputOption::VALUE_OPTIONAL, 'The plugin to export')
            ->setHelp('The <info>%command.name%</info> saves the current config of plugins to file.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $plugin = $input->getOption('plugin');

        $pluginConfig = new PluginConfig(Shopware()
            ->Models()
            ->getRepository('Shopware\Models\Plugin\Plugin'));

        $state = $pluginConfig->getState($plugin);

        $output->writeln(json_encode($state, JSON_PRETTY_PRINT));
    }
}
