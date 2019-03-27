<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WndevDeveloper\Components\Pluginlist;

/**
 * Class PluginlistInstallCommand
 *
 * @package WndevDeveloper\Commands
 */
class PluginlistInstallCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:pluginlist:install')
            ->setDescription('Install plugins from pluginlist file.')
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'The pluginlist filename to install plugins from',
                \dirname(__DIR__, 5) . '' . '/config/' . Pluginlist::DEFAULT_PLUGINLIST_FILENAME
            )
            ->setHelp('The <info>%command.name%</info> installs plugins from pluginlist file.')
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
            $reinstalled = 0;
            $updated = 0;
            $disabled = 0;
            foreach ($pluginlistData as $name => $state) {
                if ($state['reinstall'] && $pluginlist->installPlugin($name)) {
                    $output->writeln('> ' . $name . ' (re)installed');
                    $reinstalled++;
                }
                if ($state['update'] && $pluginlist->updatePlugin($name)) {
                    $output->writeln('> ' . $name . ' updated');
                    $updated++;
                }
                if ($state['disable'] && $pluginlist->disablePlugin($name)) {
                    $output->writeln('> ' . $name . ' disabled');
                    $disabled++;
                }
            }
            if (!empty($reinstalled)) {
                $output->writeln($reinstalled . ' plugin(s) (re)installed');
            }
            if (!empty($updated)) {
                $output->writeln($updated . ' plugin(s) updated');
            }
            if (!empty($disabled)) {
                $output->writeln($disabled . ' plugin(s) deactivated');
            }
        }
    }
}
