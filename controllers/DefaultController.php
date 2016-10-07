<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 21/07/16
 * Time: 09:55
 */
class DefaultController extends Controller
{
    public function homeAction()
    {
        $productsController = new ProductsController();

        $productsController ->showAllAction();

        //$productsController ->showOneProductAction();
    }
    /*
    public function researchAction()
    {
        $productsController = new ProductsController();
        $productsController ->showResearchResultAction();
    }
    */
    

}