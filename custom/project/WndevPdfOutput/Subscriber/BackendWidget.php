<?php

namespace WndevPdfOutput\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendWidget implements SubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
    * @return array
    */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatch_Backend_Index' => 'extendsBackendWidget',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_WndevPdfOutputWidget' => 'getBackendWidgetController'
        );
    }

    public function extendsBackendWidget(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        if ($controller->Request()->getActionName() !== 'index') {
            return;
        }

        $dir = $this->container->getParameter('wndev_pdf_output.plugin_dir');
        $controller->View()->addTemplateDir($dir . '/Resources/views/');
        $controller->View()->extendsTemplate('backend/widgets/wndev_pdf_output.js');
    }

    /**
    * Register the backend widget controller
    *
    * @param   \Enlight_Event_EventArgs $args
    * @return  string
    */
    public function getBackendWidgetController(\Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controllers/Backend/WndevPdfOutputWidget.php';
    }
}
