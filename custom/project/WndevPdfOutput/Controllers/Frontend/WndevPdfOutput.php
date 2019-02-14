<?php

/**
 * Frontend controller
 */
class Shopware_Controllers_Frontend_WndevPdfOutput extends Enlight_Controller_Action
{
    public function indexAction()
    {
//        $this->Response()->setHeader('Content-type', 'application/pdf', true); // application/pdf
        $id = $this->request->get('id');
        $modules = $this->container->get('modules');

        try {
            $sArticle = $modules->Articles()->sGetArticleById($id);

            if (!$sArticle) {
                throw new \InvalidArgumentException(sprintf('article "%s" not found', $id));
            }

//            var_dump($sArticle);
            $this->View()->assign('sArticle', $sArticle);

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
            die();
            $this->redirect(['controller' => 'index']);
        }

    }
}
