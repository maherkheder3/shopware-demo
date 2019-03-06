<?php

/**
 * Frontend controller
 */
class Shopware_Controllers_Frontend_customImage extends Enlight_Controller_Action
{
    public function indexAction()
    {
        try {
//            $connection = $this->get('dbal_connection');

//            $qb = $connection->createQueryBuilder();
//
//            $tt = $qb->select(['name'])
//                ->from('s_articles')
//                ->setMaxResults(20)
//                ->execute()
//                ->fetchAll(\PDO::FETCH_COLUMN);

//            $this->view->assign('names', $tt);

            $pns = $this->get('custom_image.services.product_name_service');
            $this->view->assign('names', $pns->getProductionName());


        } catch (Exception $e) {
        }
        // Assign a template variable
        // Assign a template variable
        $this->View()->assign('name', 'nein');
    }
}
