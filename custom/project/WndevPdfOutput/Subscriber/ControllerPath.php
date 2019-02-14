<?php

namespace WndevPdfOutput\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerPath implements SubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return array(
                        'Enlight_Controller_Dispatcher_ControllerPath_Frontend_WndevPdfOutput' => 'onGetControllerPathFrontend',        );
    }


    /**
     * Register the backend controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_WndevPdfOutput     */
    public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args)
    {
        $this->container->get('template')->addTemplateDir(__DIR__
            . '/../Resources/views/');
        return __DIR__ . '/../Controllers/Backend/WndevPdfOutput.php';
    }

    /**
     * Register the frontend controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Frontend_WndevPdfOutput     */
    public function onGetControllerPathFrontend(\Enlight_Event_EventArgs $args)
    {
        $this->container->get('template')->addTemplateDir(__DIR__
            . '/../Resources/views/');
        return __DIR__ . '/../Controllers/Frontend/WndevPdfOutput.php';
    }
}
