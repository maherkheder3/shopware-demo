<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WndevDeveloper\Components\PluginConfig;

class PluginConfigInstallCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:pluginconfig:install')
            ->setDescription('Loads the given config and persist it to db.')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'The filename to load the config from'
            )
            ->setHelp('The <info>%command.name%</info> loads the current config of plugins from file.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        $json = file_get_contents($filename);

        $payload = json_decode($json, true);

        if ($payload === null) {
            $output->writeln('Unable to parse config file');

            return;
        }

        $pluginConfig = new PluginConfig(Shopware()
            ->Models()
            ->getRepository('Shopware\Models\Plugin\Plugin'));
        $pluginConfig->setState($payload);
    }
}
