<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LessToPhp
 *
 * @package WndevDeveloper\Commands
 */
class LessToPhp extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:less:to-php')
            ->setDescription('Copy LESS variables from "colors.less" to "Theme.php"')
            ->addArgument('theme', InputArgument::REQUIRED, 'The folder name of the theme, e.g. "Default"')
            ->setHelp('The <info>%command.name%</info> copies LESS variables from "colors.less" to "Theme.php"')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $theme = basename($input->getArgument('theme'));

        $pathTheme = Shopware()->DocPath() . 'themes/Frontend/' . $theme;
        if (!file_exists($pathTheme) || !is_dir($pathTheme)) {
            throw new \RuntimeException('Theme path does not exist: ' . $pathTheme);
        }

        $fileTheme = $pathTheme . '/Theme.php';
        if (!file_exists($fileTheme) || !is_file($fileTheme)) {
            throw new \RuntimeException('Theme.php does not exist: ' . $fileTheme);
        }

        $pathLess = $pathTheme . '/frontend/_public/src/less/_variables';
        if (!file_exists($pathLess) || !is_dir($pathLess)) {
            throw new \RuntimeException('LESS path does not exist: ' . $pathLess);
        }

        $filesLess = glob($pathLess . '/*.less');
        if (empty($filesLess)) {
            throw new \RuntimeException('No LESS files found in ' . $pathLess);
        }

        $variables = [];
        foreach ($filesLess as $fileLess) {
            $handleLess = fopen($fileLess, 'rb');
            $inComments = false;
            if (!$handleLess) {
                throw new \RuntimeException('LESS file could not be read: ' . $fileLess);
            }

            while (($buffer = fgets($handleLess, 4096)) !== false) {
                if (preg_match('#/\*#', $buffer)) {
                    $inComments = true;
                } elseif (preg_match('#\*/#', $buffer)) {
                    $inComments = false;
                } elseif (!$inComments && preg_match('#^\s*@([a-zA-Z0-9_-]+?):\s*(.+?);\s*$#', $buffer, $matches)) {
                    $variables[$matches[1]] = $matches[2];
                }
            }
            fclose($handleLess);
        }

        foreach ($variables as $key => $value) {
            if (preg_match('#^\@(\S+)#', $value, $varMatch) && empty($variables[$varMatch[1]])) {
                throw new \RuntimeException('Undefined (but used) LESS variable: @' . $varMatch[1]);
            }
        }

        if (empty($variables)) {
            throw new \RuntimeException('Did not find variables in ' . $pathLess);
        }

        $contentTheme = file_get_contents($fileTheme);
        $contentTheme = preg_replace(
            '#(\n\s*?)(private \$themeColorDefaults\s*=\s*).+?(;)#s',
            '$1$2' . str_replace("\n", "\n      ", var_export($variables, true)) . '$3',
            $contentTheme
        );

        $success = file_put_contents($fileTheme, $contentTheme);
        if (!$success) {
            throw new \RuntimeException('Could not write file: ' . $fileTheme);
        }
        $output->writeln('The theme has been written.');
    }
}
