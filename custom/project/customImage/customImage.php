<?php

namespace customImage;

use Shopware\Components\Plugin;
use Shopware\Models\Media\Media;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Shopware-Plugin customImage.
 */
class customImage extends Plugin
{
    /**
     * Adds the widget to the database and creates the database schema.
     *
     * @param Plugin\Context\InstallContext $installContext
     */
    public function install(Plugin\Context\InstallContext $installContext)
    {
//      parent::install($installContext);

        $attributeService = $this->container->get('shopware_attribute.crud_service');
        
        $attributeService->get('s_articles_attributes', 'alt_image');

        $attributeService->update('s_articles_attributes', 'alt_image', 'single_selection', [
            'displayInBackend' => true,
            'entity' => Media::class,
            'label' => 'Meine Bilder'
        ]);


        $attributeService->get('s_articles_attributes', 'my_first_column');
        $attributeService->update('s_articles_attributes', 'my_first_column', 'combobox', [
            'label' => 'Field label',
            'supportText' => 'Value under the field my_first_column',
            'helpText' => 'Value which is displayed inside a help icon tooltip',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => true,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //in case of multi_selection or single_selection type, article entities can be selected,
            'entity' => 'Shopware\Models\Article\Article',

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => true,

            //in case of combo box type, defines the selectable values
            'arrayStore' => [
                ['key' => '1', 'value' => 'first value'],
                ['key' => '2', 'value' => 'second value']
            ],
        ]);

        $this->container->get('models')->generateAttributeModels(['s_articles_attributes']);
        $installContext->scheduleClearCache(Plugin\Context\InstallContext::CACHE_LIST_ALL);

//        $this->createSchema();
    }

    /**
     * Remove widget and remove database schema.
     *
     * @param Plugin\Context\UninstallContext $uninstallContext
     */
    public function uninstall(Plugin\Context\UninstallContext $uninstallContext)
    {
//      parent::uninstall($uninstallContext);

        if($uninstallContext->keepUserData()){
            return;
        }

        $attributeService = $this->container->get('shopware_attribute.crud_service');
        $attributeExists = $attributeService->get('s_articles_attributes', 'alt_image');

        if($attributeExists === NULL){
            return;
        }

        $attributeService->delete('s_articles_attributes', 'alt_image');
        $attributeService->delete('s_articles_attributes', 'my_first_column');
        $this->container->get('models')->generateAttributeModels(['s_articles_attributes']);
        $uninstallContext->scheduleClearCache(Plugin\Context\InstallContext::CACHE_LIST_ALL);

        $this->removeSchema();
    }

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('custom_image.plugin_dir', $this->getPath());
        parent::build($container);
    }

    /**
     * creates database tables on base of doctrine models
     */
    private function createSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\customImage\Models\Image::class)
        ];
        $tool->createSchema($classes);
    }

    private function removeSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\customImage\Models\Image::class)
        ];
        $tool->dropSchema($classes);
    }
}
