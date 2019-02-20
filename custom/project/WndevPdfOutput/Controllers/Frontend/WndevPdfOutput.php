<?php

use Dompdf\Dompdf;

/**
 * Frontend controller
 */
class Shopware_Controllers_Frontend_WndevPdfOutput extends Enlight_Controller_Action
{
    public function indexAction()
    {
        $this->Response()->setHeader('Content-type', 'application/pdf', true); // application/pdf
        $id = $this->request->get('id');
        $modules = $this->container->get('modules');

        try {
            $sArticle = $modules->Articles()->sGetArticleById($id);

            if (!$sArticle) {
                throw new \InvalidArgumentException(sprintf('article "%s" not found', $id));
            }

            $config = $this->getPluginConfig();

            $blockedProperties = $config['blockedProperties'];
            $blockedProperties = explode(';', $blockedProperties);

            foreach ($sArticle['sProperties'] as $key => $sProperty) {
                if (in_array($sProperty['name'], $blockedProperties)) {
                    unset($sArticle['sProperties'][$key]);
                }
            }

            $this->View()->assign('sArticle', $sArticle);

            $data = $this->View()->fetch(dirname(__FILE__) . '/../../Resources/views/frontend/wndev_pdf_output/index.tpl');

            $pdfConfig = [
                'mode' => 'c',
                'format' => 'A4',
                'default_font_size' => 0,
                'default_font' => 'verdana',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 35,
                'margin_bottom' => 25,
                'margin_header' => 0,
                'margin_footer' => 0,
                'orientation' => 'P',
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

            // prevent shopware from template rendering
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
