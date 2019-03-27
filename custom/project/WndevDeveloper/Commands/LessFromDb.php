<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LessFromDb
 *
 * @package WndevDeveloper\Commands
 */
class LessFromDb extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:less:from-db')
            ->setDescription('Build LESS variables from DB to "colors.less"')
            ->addArgument('theme', InputArgument::REQUIRED, 'The folder name of the theme, e.g. "Default"')
            ->setHelp('The <info>%command.name%</info> copies LESS variables from DB to "colors.less"')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $theme = basename($input->getArgument('theme'));

        $pathTheme = Shopware()->DocPath() . 'themes/Frontend/' . $theme;
        if (!file_exists($pathTheme) || !is_dir($pathTheme)) {
            throw new \RuntimeException('Theme path does not exist: ' . $pathTheme);
        }

        $pathLess = $pathTheme . '/frontend/_public/src/less/_variables';
        if (!mkdir($pathLess, 0775, true) && !is_dir($pathLess)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $pathLess));
        }

        $filenames = [
            'color',
            'typography',
            'buttons',
            'forms',
            'tables',
        ];

        $header = '// Automatically created by \'bin/console wndev:less:from-db ' . $theme . "'\n";
        $fileLess = [];
        $handleLess = [];

        foreach ($filenames as $f) {
            $fileLess[$f] = $pathLess . '/' . $f . '.less';
            $handleLess[$f] = fopen($fileLess[$f], 'wb');
            if (empty($handleLess[$f])) {
                throw new \RuntimeException('LESS files could not be written: ' . $fileLess[$f]);
            }
            fwrite($handleLess[$f], $header);
        }

        $sql = 'SELECT e.name, v.value FROM s_core_templates_config_values AS v'
            . ' JOIN s_core_templates_config_elements AS e ON v.element_id = e.id'
            . ' WHERE less_compatible = 1'
            . ' ORDER BY e.id';

        $variables = [];
        foreach (Shopware()
            ->Db()
            ->query($sql) as $row) {
            $value = trim(unserialize($row['value']));
            if ($value !== '') {
                $handleName = 'color';
                if (preg_match(
                    '#^(font|btn|panel|label|input|panel-table|panel|table|badge)\-#',
                    $row['name'],
                    $matches
                )) {
                    switch ($matches[1]) {
                        case 'panel-table':
                        case 'table':
                        case 'badge':
                            $handleName = 'tables';
                            break;
                        case 'btn':
                        case 'panel':
                            $handleName = 'buttons';
                            break;
                        case 'label':
                        case 'input':
                            $handleName = 'forms';
                            break;
                        default:
                            $handleName = 'typography';
                            break;
                    }
                }

                $line = '@' . $row['name'] . ': ' . $value . ';' . "\n";
                $variables[$row['name']] = $value;
                fwrite($handleLess[$handleName], $line);
                #echo($line);
            }
        }
        foreach ($filenames as $f) {
            fclose($handleLess[$f]);
        }

        foreach ($variables as $key => $value) {
            if (preg_match('#^\@(\S+)#', $value, $varMatch) && empty($variables[$varMatch[1]])) {
                throw new \RuntimeException('Undefined (but used) LESS variable: @' . $varMatch[1]);
            }
        }

        $output->writeln('LESS files written.');
    }
}
