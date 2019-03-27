<?php

namespace WndevDeveloper\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginDocumentation
 *
 * @package WndevDeveloper\Commands
 */
class PluginDocumentation extends ShopwareCommand
{
    const AUTHOR_NAME = 'web-netz GmbH';
    const AUTHOR_URL  = 'https://www.web-netz.de/';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('wndev:plugin:documentation')
            ->setDescription('Convert README.md into plugin.xml text.')
            ->addArgument('plugin', InputArgument::REQUIRED, 'The plugin name. Set to `all` to fix all custom plugins.')
            ->setHelp('The <info>%command.name%</info> converts README.md into plugin.xml text.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Find plugin path
        $plugin = basename($input->getArgument('plugin'));
        $output->writeln($plugin);

        if ($plugin === 'all') {
            $directories = glob(\dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            foreach ($directories as $plugin) {
                $this->writeDocumentation(basename($plugin), $output);
            }
        } else {
            $this->writeDocumentation($plugin, $output);
        }
    }

    /**
     * @param                 $plugin
     * @param OutputInterface $output
     */
    private function writeDocumentation($plugin, OutputInterface $output)
    {
        $path = \dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $plugin;
        if (!is_dir($path)) {
            throw new \RuntimeException('Plugin path not found: ' . $path);
        }

        // Find README file
        $year = date('Y');
        $fileReadme = $path . DIRECTORY_SEPARATOR . 'Readme.md';
        if (!file_exists($fileReadme)) {
            $fileReadme = $path . DIRECTORY_SEPARATOR . 'README.md';
        }
        if (!file_exists($fileReadme)) {
            file_put_contents($fileReadme, $this->getReadmeStub($plugin, $year));
            $output->writeln('Wrote ' . $fileReadme);
        }

        // Find plugin.xml
        $fileXml = $path . DIRECTORY_SEPARATOR . 'plugin.xml';
        if (!file_exists($fileXml)) {
            file_put_contents($fileXml, $this->getXmlStub($plugin, $year));
        }

        $readmeHtml = $this->convertMarkdown(file_get_contents($fileReadme));
        $xml = preg_replace(
            '#(<description[^>]*>).*?(</description>)#s',
            '$1<![CDATA[' . $this->getHtmlHeader(basename($fileReadme)) . "\n" . $readmeHtml . ']]>$2',
            file_get_contents($fileXml)
        );

        file_put_contents($fileXml, $xml);

        $output->writeln('Wrote ' . $fileXml);
    }

    /**
     * @param $pluginName
     * @param $year
     *
     * @return string
     */
    private function getReadmeStub($pluginName, $year): string
    {
        $authorName = self::AUTHOR_NAME;
        $authorUrl = self::AUTHOR_URL;

        return <<<"TAG"
$pluginName
===========================

Lorem ipsum...

Konfiguration
-------------

* **Option 1**: Lorem ipsum...
* **Option 2**: Lorem ipsum...

Autor / Lizenz
--------------

Copyright © $year, [$authorName]($authorUrl)

Proprietäre Lizenz

TAG;
    }

    /**
     * @param $plugin
     * @param $year
     *
     * @return string
     */
    private function getXmlStub($plugin, $year): string
    {
        $plugin = htmlspecialchars($plugin);
        $year = htmlspecialchars($year);
        $authorName = htmlspecialchars(self::AUTHOR_NAME);
        $authorUrl = htmlspecialchars(self::AUTHOR_URL);

        return <<<"TAG"
<?xml version="1.0" encoding="utf-8"?>
<plugin xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/shopware/shopware/5.3/engine/Shopware/Components/Plugin/schema/plugin.xsd">
    <label>$plugin</label>
    <version>0.0.1</version>
    <copyright>Copyright © $year, $authorName</copyright>
    <license>Proprietary</license>
    <link>$authorUrl</link>
    <author>$authorName</author>
    <description>Lorem ipsum...</description>
    <compatibility minVersion="5.2.0"/>
    <changelog version="0.0.1">
        <changes>Initial release</changes>
    </changelog>
</plugin>

TAG;
    }

    /**
     * @param $filenameReadme
     *
     * @return string
     */
    private function getHtmlHeader($filenameReadme): string
    {
        $date = date('c');

        return <<<"TAG"
<!-- Generated from $filenameReadme at $date by WndevDeveloper -->
<style>
.documentation h1 { font-size: 1.8em; }
.documentation h2 { font-size: 1.5em; }
.documentation h3 { font-size: 1.2em; }
.documentation h2,.documentation h3,.documentation h4 { margin-top: 1em; margin-bottom: 0.5em; }
.documentation p, .documentation ul, .documentation pre, .documentation li { margin-bottom: 0.5em; }
.documentation a { color: #0e6bbb; }
.documentation pre, .documentation code { background: #ddd; padding: 0 0.25em; }
.documentation pre { overflow: auto; }
.documentation strong { font-weight: bold; }
</style>
TAG;
    }

    /**
     * @param $str
     *
     * @return string
     */
    private function convertMarkdown($str): string
    {
        require_once __DIR__ . '/../vendor/parsedown/Parsedown.php';
        $Parsedown = new \Parsedown();
        $html = $Parsedown->text($str);

        $html = preg_replace('#<img[^>]+>#s', '', $html);
        $html = preg_replace('#<p>\s*</p>\s*#s', '', $html);

        return '<div class="documentation">' . $html . '</div>';
    }
}
