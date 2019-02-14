<?php

class Shopware_Controllers_Frontend_SabPDFTool extends Enlight_Controller_Action {

    private $admin = null;
    private $basket = null;
    var $session = null;

    public function init() {
        $this->admin = Shopware()->Modules()->Admin();
        $this->basket = Shopware()->Modules()->Basket();
        $this->session = Shopware()->Session();
        $this->View()->addTemplateDir(dirname(__FILE__) . "/../../views/");
	    // include_once Shopware()->DocPath() . 'engine/Library/Mpdf/mpdf.php';
    }

    public function indexAction() {

    }

    public function pdfdetailAction() {

        $articleID              = intval( $this->Request()->articleID );
        $mode                   = strval($this->Request()->mode);
        $article                = Shopware()->Modules()->Articles()->sGetArticleById($articleID);
        $this->View()->sArticle = $article;
        $this->View()->sHeute   = date("d.m.Y",time());

        $config     = Shopware()->Plugins()->Frontend()->SabPDFTool()->Config();
        $this->View()->SabPDFToolConfig = $config;

        $sql = "SELECT s_articles_attributes.attr11,
                    s_articles_attributes.attr12,
                    s_articles_details.id,
                    s_articles_details.articleID,
                    s_articles_details.ordernumber,
                    s_articles_details.suppliernumber,
                    s_core_units.unit,
                    s_core_units.description,
                    s_articles_details.purchaseunit,
                    s_articles_details.referenceunit,
                    s_articles_details.packunit,
                    s_articles_details.shippingfree,
                    s_articles_details.shippingtime,
                    s_articles_details.ean,
                    s_articles_details.additionaltext,
                    s_articles_prices.price,
                    s_articles_prices.pseudoprice,
                    s_articles_prices.baseprice
                FROM s_articles_details LEFT JOIN s_core_units ON s_articles_details.unitID = s_core_units.id
                     INNER JOIN s_articles_attributes ON s_articles_details.id = s_articles_attributes.articledetailsID
                     INNER JOIN s_articles_prices ON s_articles_details.id = s_articles_prices.articledetailsID
                WHERE s_articles_details.articleID = ? AND s_articles_details.active = '1' AND s_articles_prices.`from` = '1' AND s_articles_prices.pricegroup = ?";

        $result = Shopware()->Db()->fetchAll($sql , array($articleID, 'EK'));

        $liste  = array();

        foreach ($result as $value) {

            $additionaltext         = "";
            $liste[$value['id']]    = $value; // Werte ins array schreiben

            $sql = "SELECT s_article_configurator_options.`name`,
                        s_article_configurator_options.id
                    FROM s_article_configurator_option_relations
                    INNER JOIN s_article_configurator_options ON s_article_configurator_option_relations.option_id = s_article_configurator_options.id
                    WHERE article_id = ?";
            $result_optionen = Shopware()->Db()->fetchAll($sql , array($value['id']));

            if (count($result_optionen) >= 1) {
                $i=1;
                foreach ($result_optionen as $wert) {
                    if ($i>1) {
                        $additionaltext .= ", ";
                    }
                    $additionaltext .= $wert['name'];
                    $i++;
                }
            }

            $liste[$value['id']]['additionaltext'] = $additionaltext;
        }

        $this->View()->sVariantenListe = $liste;

        $data = $this->View()->fetch(dirname(__FILE__) . '/../../views/frontend/documents/pdfDetail.tpl');
        $config = [
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
        ];

        $mpdf                   = new Mpdf\Mpdf($config);
        $mpdf->allow_charset_conversion = true;  // Set by default to TRUE
        $mpdf->charset_in       = 'utf-8';
        $mpdf->mirrorMargins    = 0;
        $mpdf->showImageErrors  = false;
        $mpdf->debug            = false;

        $footer = '<div align="center">See <a href="http://mpdf1.com/manual/index.php">documentation manual</a></div>';

        // $mpdf->defaultheaderfontsize = 8;
        // $mpdf->defaultheaderfontstyle = B;
        // $mpdf->defaultheaderline = 1;
        // $mpdf->defaultfooterfontsize = 8;
        // $mpdf->defaultfooterfontstyle = B;
        // $mpdf->defaultfooterline = 1;
        // $mpdf->SetHeader('{DATE j.m.Y}|{PAGENO}|' . $this->View()->sOrderNumber);

        $mpdf->WriteHTML($data);

        // $mpdf->SetHTMLFooter($footer,'ALL');
        // $mpdf->SetFooter(Shopware()->Snippets()->getSnippet($PLUGIN_NAMESPACE)->get('Footer'));

        $mpdf->Output('Produktinformation.pdf', 'I');
        exit();
    }

}
