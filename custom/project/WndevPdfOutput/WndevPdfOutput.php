<?php

namespace WndevPdfOutput;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Models\Widget\Widget;

/**
 * Shopware-Plugin WndevPdfOutput.
 */
class WndevPdfOutput extends Plugin
{
    /**
     * Adds the widget to the database and creates the database schema.
     *
     * @param Plugin\Context\InstallContext $installContext
     */
    public function install(Plugin\Context\InstallContext $installContext)
    {
        parent::install($installContext);
        $repo = $this->container->get('models')->getRepository(\Shopware\Models\Plugin\Plugin::class);
        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = $repo->findOneBy([ 'name' => 'WndevPdfOutput' ]);

        $widget = new Widget();
        $widget->setName('wndev_pdf_output');
        $widget->setPlugin($plugin);

        $plugin->getWidgets()->add($widget);


    }

    /**
     * Remove widget and remove database schema.
     *
     * @param Plugin\Context\UninstallContext $uninstallContext
     */
    public function uninstall(Plugin\Context\UninstallContext $uninstallContext)
    {
        parent::uninstall($uninstallContext);
        $modelManager = $this->container->get('models');
        $repo = $modelManager->getRepository(Widget::class);

        $widget = $repo->findOneBy([ 'name' => 'wndev_pdf_output' ]);
        $modelManager->remove($widget);
        $modelManager->flush();

    }

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('wndev_pdf_output.plugin_dir', $this->getPath());
        parent::build($container);
    }

}
