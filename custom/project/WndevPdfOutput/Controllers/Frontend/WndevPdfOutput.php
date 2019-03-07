<?php

use Dompdf\Dompdf;

/**
 * Frontend controller
 */
class Shopware_Controllers_Frontend_WndevPdfOutput extends Enlight_Controller_Action
{
    /** @var \Shopware_Components_Modules */
    private $modules;
    public function __construct(
        \Enlight_Controller_Request_Request $request,
        \Enlight_Controller_Response_Response $response
    ) {
        parent::__construct($request, $response);
        $this->view->extendsTemplate('frontend/documents/detail.tpl');
    }

    public function preDispatch()
    {
        $this->modules = $this->get('modules');
    }

    public function indexAction()
    {
        $this->Response()->setHeader('Content-type', 'application/pdf', true); // application/pdf
        $id = $this->request->get('articleId');

        try {
            $sArticle = $this->modules->Articles()->sGetArticleById($id);

            if (!$sArticle) {
                throw new \InvalidArgumentException(sprintf('article "%s" not found', $id));
            }

            $config = $this->getPluginConfig();

            // get blocked properties from configuration the plugin
            $blockedProperties = $config['blockedProperties'];
            $blockedProperties = explode(';', $blockedProperties);

            // clear all blocked properties from sArticle
            foreach ($sArticle['sProperties'] as $key => $sProperty) {
                if (in_array($sProperty['name'], $blockedProperties)) {
                    unset($sArticle['sProperties'][$key]);
                }
            }
            $this->View()->assign('sArticle', $sArticle);

            // save just the unblock properties in newAttr
            $newAttr = [];
            for ($i = 1; $i <= 20; $i++ ) {
                if($sArticle['attr' . $i] && $sArticle['attr' . $i] !== null &&
                    in_array('attr' . $i, $blockedProperties) === false){
                    $newAttr[] = $sArticle['attr' . $i];
                }
            }
            $this->View()->assign('sAttrs', $newAttr);
            $this->View()->assign('shopLogo', $config['logo']);
            $this->View()->assign('logoCSS', $config['logoCSS']);


            // pdf configuration
            $data = $this->View()->fetch(dirname(__FILE__) . '/../../Resources/views/frontend/wndev_pdf_output/index.tpl');
            $pageMargin = explode(';', $config['pageMargin']);
            $pdfConfig = [
                'mode' => 'c',
                'format' => $config['format'],
                'default_font_size' => $config['default_font_size'],
                'default_font' => $config['verdana'],
                'margin_top' => $pageMargin[0],
                'margin_right' => $pageMargin[1],
                'margin_bottom' => $pageMargin[2],
                'margin_left' => $pageMargin[3],
                'margin_header' => $config['marginHeader'],
                'margin_footer' => $config['marginFooter'],
                'orientation' => $config['orientation'],
                'debug' => true,
                'allow_output_buffering' => true
            ];
            $mpdf                   = new Mpdf\Mpdf($pdfConfig);
            $mpdf->allow_charset_conversion = true;  // Set by default to TRUE
            $mpdf->charset_in       = 'utf-8';
            $mpdf->mirrorMargins    = 0;
            $mpdf->showImageErrors  = false;
            $mpdf->debug            = false;

            $mpdf->WriteHTML($data);

            $mpdf->Output('Produktinformation.pdf', 'I');

            // prevent shopware from template rendering ( important )
            exit;

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
            die();
            $this->redirect(['controller' => 'index']);
        }
    }

    /**
     * @return array
     * get plugin config for every shop and languages
     */
    public function getPluginConfig()
    {
        $modelManager = $this->container->get('models');
        $contextService     = $this->container->get('shopware_storefront.context_service');
        $context            = $contextService->getShopContext();

        /** @var \Shopware\Models\Shop\Shop $shop */
        $shop = $modelManager->getRepository(\Shopware\Models\Shop\Shop::class)
            ->find($context->getShop()->getId());
        $reader = $this->container->get('shopware.plugin.config_reader');
        $config = $reader->getByPluginName('WndevPdfOutput', $shop);
        return $config;
    }
}
