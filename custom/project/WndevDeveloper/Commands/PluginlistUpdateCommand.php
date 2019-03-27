<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WndevDeveloper\Components\Pluginlist;

/**
 * Class PluginsSaveCommand
 *
 * @package WndevDeveloper\Commands
 */
class PluginlistUpdateCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:pluginlist:update')
            ->setDescription('Save current state of plugins to pluginlist file for later retrieval.')
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'The filename to store pluginlist into',
                dirname(__DIR__, 5) . '' . '/config/' . Pluginlist::DEFAULT_PLUGINLIST_FILENAME
            )
            ->addOption(
                'plugin',
                null,
                InputOption::VALUE_OPTIONAL,
                'If given, this will only update the given plugin.',
                ''
            )
            ->setHelp('The <info>%command.name%</info> saves the current state of plugins to pluginlist file.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $onlyThisPlugin = $input->getOption('plugin');

        $pluginlist = new Pluginlist();
        $success = $pluginlist->writeFileWithCurrentState($filename, $onlyThisPlugin);
        $output->writeln($success ? $filename . ' updated' : $filename . ' could not be written');
    }
}
