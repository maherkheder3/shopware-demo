<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Shopware_Plugins_Frontend_SabPDFTool_Bootstrap extends Shopware_Components_Plugin_Bootstrap {

    var $session = null;

    /**
     * Gibt die aktuelle Version zurück
     * @return string
     */
    function getVersion() {
        return '1.0.1';
    }

    /**
     * Installation des Plugins und Erstellung der Hooks
     * @return bool
     */
    function install() {
        // Event für Detailseite
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend_Detail', 'onPostDispatch');
        //$this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend_Checkout', 'onPostDispatchSabPDFTool');

        // Event für Controller
        $this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_SabPDFTool', 'onGetControllerPathFrontend');
        
        $this->createForm();
        //$this->initPluginSnippet();
        return true;
    }

    function createForm() {
        // Konfigurationsformular
        $form = $this->Form();
        $form->setElement('text', 'pdf_logo', array('label' => 'Pfad zum PDF-Logo', 'value' => '', 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP));
        $form->setElement('text', 'pdf_headline', array('label' => 'PDF-Titel', 'value' => 'Produktinformation', 'scope' => Shopware\Models\Config\Element::SCOPE_SHOP));
        // $form->save();
        return true;
    }

    function initPluginSnippet() {
        $namespace = 'plugins/frontend/SabPDFToolv5';
        $this->insertPluginSnippet($namespace, 'AufeinenBlick', 'Auf einen Blick');
        $this->insertPluginSnippet($namespace, 'Produktinformationen', 'Produktinformationen');
        $this->insertPluginSnippet($namespace, 'Produkteigenschaften', 'Produkteigenschaften');
        $this->insertPluginSnippet($namespace, 'Anwendungsbereiche', 'Anwendungsbereiche');
        $this->insertPluginSnippet($namespace, 'ProduktrichtlinienHinweise', 'Produktrichtlinien und Hinweise');
        $this->insertPluginSnippet($namespace, 'Hinweise', 'Hinweise');
        $this->insertPluginSnippet($namespace, 'Tipp', 'Tipp');
        $this->insertPluginSnippet($namespace, 'Produktzusammensetzung', 'Produktzusammensetzung');
        $this->insertPluginSnippet($namespace, 'Bestellinformationen', 'Bestellinformationen');
        $this->insertPluginSnippet($namespace, 'Typ', 'Typ');
        $this->insertPluginSnippet($namespace, 'ArtNr', 'ArtNr');
        $this->insertPluginSnippet($namespace, 'PZN', 'PZN');
        $this->insertPluginSnippet($namespace, 'VE', 'VE');
        $this->insertPluginSnippet($namespace, 'Preis', 'Preis');
        $this->insertPluginSnippet($namespace, 'Verordnungshinweis', 'Verordnungshinweis');
        return true;
    }

    function insertPluginSnippet($namespace, $name, $value) {

        $defaulValue = 1;
        $getDefaultValues = 'SELECT id as `shopID`, locale as `localeID` FROM `s_core_multilanguage` WHERE `default` = \'' . $defaulValue . '\' ';
        $getDefaultValues = Shopware()->Db()->fetchOne($getDefaultValues);


        $shopID = $getDefaultValues['shopID'];
        $localID = $getDefaultValues['localID'];
        $date = date('y-m-d H:i:s', time());

        $insert = $this->Application()->Db()->query('
            INSERT INTO `s_core_snippets` (`namespace`,`shopID`,`localeID`,`name`,`value`,`created`)
            VALUES(?,?,?,?,?,?)', array($namespace, $shopID, $localID, $name, $value, $date));
    }

    /**
     * Metainformationen des Plugins extern laden und zurückgeben
     * @return mixed
     */
    function getInfo() {
        return include(dirname(__FILE__) . '/Meta.php');
    }

    function uninstall() {
        $result = Shopware()->Db()->fetchRow('SELECT id FROM s_core_config_forms WHERE name = ?', array('SabPDFTool'));
        $id = $result['id'];
        Shopware()->Db()->query('DELETE FROM `s_core_config_elements` WHERE form_id = ?', array($id));
        //Shopware()->Db()->query('DELETE FROM `s_core_snippets` WHERE `namespace` = \'plugins/frontend/SabPDFToolv5\'');
        return true;
    }

    public function onPostDispatch(Enlight_Event_EventArgs $args) {

        $this->session = Shopware()->Session();

        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();
        $view = $args->getSubject()->View();

        if ($request->getControllerName() !== 'detail'
            || $request->getModuleName() !== 'frontend'
            || !$view->hasTemplate()) {
            return;
        }

        $view->addTemplateDir($this->Path() . 'views/');
        $view->extendsTemplate('frontend/documents/detail.tpl');

    }

    public function onGetControllerPathFrontend(Enlight_Event_EventArgs $args) {
        return $this->Path() . 'Controllers/Frontend/SabPDFTool.php';
    }

}
