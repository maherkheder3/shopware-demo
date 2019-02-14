<?php

class Shopware_Controllers_Backend_WndevPdfOutputWidget extends Shopware_Controllers_Backend_ExtJs
{
    /**
    * Loads data for the backend widget
    */
    public function loadBackendWidgetAction()
    {
        $data = array(
            array('id' => 1, 'name' => 'Shopware'),
            array('id' => 2, 'name' => 'Shopman'),
        );

    $this->View()->assign(array(
            'success' => true,
            'data' => $data
        ));
    }
}
