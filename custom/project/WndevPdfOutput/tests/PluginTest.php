<?php

namespace WndevPdfOutput\Tests;

use WndevPdfOutput\WndevPdfOutput as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'WndevPdfOutput' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['WndevPdfOutput'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
