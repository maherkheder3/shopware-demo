<?php

namespace customImage\Tests;

use customImage\customImage as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'customImage' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['customImage'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
