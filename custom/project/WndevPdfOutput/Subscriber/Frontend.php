<?php

namespace WndevPdfOutput\Subscriber;

use Enlight\Event\SubscriberInterface;

class Frontend implements SubscriberInterface
{
    /** @var string */
    private $pluginDir;

    /**
     * Frontend constructor.
     *
     * @param $pluginDir
     */
    public function __construct($pluginDir)
    {
        $this->pluginDir = $pluginDir;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend_Detail' => 'onPreDispatchFrontendDetail'
        ];
    }

    public function onPreDispatchFrontendDetail(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Detail $subject */
        $subject = $args->getSubject();
        $view = $subject->View();

        $view->addTemplateDir($this->pluginDir.'/Resources/views/');
    }
}
