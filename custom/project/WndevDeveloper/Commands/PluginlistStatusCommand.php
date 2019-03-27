<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WndevDeveloper\Components\Pluginlist;

/**
 * Class PluginlistStatusCommand
 *
 * @package WndevDeveloper\Commands
 */
class PluginlistStatusCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:pluginlist:status')
            ->setDescription('Check current state of plugins compared to pluginlist file.')
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'The pluginlist filename to check against',
                dirname(__DIR__, 5) . '' . '/config/' . Pluginlist::DEFAULT_PLUGINLIST_FILENAME
            )
            ->setHelp('The <info>%command.name%</info> checks the current state of plugins'
                . ' compared to pluginlist file.')
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        $pluginlist = new Pluginlist();
        $pluginlistData = $pluginlist->compareFileWithCurrentState($filename);
        if (!$pluginlistData) {
            $output->writeln($filename . ' not found or empty');
        } else {
            $reinstallsNeeded = 0;
            $updatesNeeded = 0;
            $disablesNeeded = 0;
            foreach ($pluginlistData as $name => &$state) {
                if ($state['reinstall']) {
                    $reinstallsNeeded++;
                }
                $state['reinstall'] = $state['reinstall'] ? 'Yes' : 'No';
                if ($state['update']) {
                    $updatesNeeded++;
                }
                $state['update'] = $state['update'] ? 'Yes' : 'No';
                if ($state['disable']) {
                    $disablesNeeded++;
                }
                $state['disable'] = $state['disable'] ? 'Yes' : 'No';
            }
            unset($state);

            $table = new Table($output);
            $table
                ->setHeaders(['Plugin', 'Needs (re)installing?', 'Needs update?', 'Needs disabling?'])
                ->setRows($pluginlistData)
                ->render()
            ;

            $output->writeln($reinstallsNeeded . ' plugins need (re)installing');
            $output->writeln($updatesNeeded . ' plugins need updating');
            $output->writeln($disablesNeeded . ' plugins need disabling');

            if ($reinstallsNeeded || $updatesNeeded || $disablesNeeded) {
                $output->writeln('wndev:pluginlist:install | Copy state of file to DB');
                $output->writeln('wndev:pluginlist:update  | Copy state of DB to file');
            }
        }
    }
}
