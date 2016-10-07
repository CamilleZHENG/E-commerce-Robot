<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 29/07/16
 * Time: 14:14
 */
class OrdersController extends Controller
{
    public function __construct()
    {
        $clientManager =   (new ClientsManager())->getConnectionState() ;

        if (!isset($clientManager))//on peut aussi mettre: $clientManager===null
        {
            header('Location:'.CLIENT_ROOT.'Clients/showClientSpace');

            exit();
        }

        $categoriesManager = new CategoriesManager();

        $this -> viewData['categories'] = $categoriesManager ->getAll();
    }


    public function addOrderInfosAction()
    {
        $billingInfos = [];
        $deliveryInfos=[];

        $billingFields = [
            'billingCivility',
            'billingFirstName',
            'billingLastName',
            'billingAddress',
            'billingZipCode',
            'billingCity',
            'billingCountry',
            'billingPhoneNumber'
        ];
        $deliveryFields =[
            'deliveryCivility',
            'deliveryFirstName',
            'deliveryLastName',
            'deliveryAddress',
            'deliveryZipCode',
            'deliveryCity',
            'deliveryCountry',
            'deliveryPhoneNumber'
        ];

        foreach ($billingFields as $value)
        {
            if(array_key_exists($value,$_POST))
            {
                $billingInfos[$value] = trim($_POST[$value]);
            }
            if (empty($billingInfos[$value]))
            {
                $errors[$value] = TRUE;
            }
        }

        foreach ($deliveryFields as $value)
        {
            if(array_key_exists($value,$_POST))
            {
                $deliveryInfos[$value] = trim($_POST[$value]);
            }
            if (empty($deliveryInfos[$value]))
            {
                $errors[$value] = TRUE;
            }
        }

        $_SESSION['form']['orders']['billing'] = $billingInfos;
        $_SESSION['form']['orders']['delivery'] = $deliveryInfos;


        if(isset($errors))
        {
            if(session_status() !== PHP_SESSION_ACTIVE)
            {
                session_start();
                session_regenerate_id();
            }

            $_SESSION['form']['orders']['error'] = $errors;

            header('Location:'.CLIENT_ROOT.'Orders/showOrderInfos');

            exit();
        }
        else
        {
            if(session_status() !== PHP_SESSION_ACTIVE)
            {
                session_start();
                session_regenerate_id();
            }

            header('Location:'.CLIENT_ROOT.'Orders/purchaseOrder');

            exit();
        }

    }
    
    
    public function showOrderInfosAction()
    {


        $categoriesManager = new CategoriesManager();

        $this -> viewData['categories'] = $categoriesManager ->getAll();

        $this -> generateView('orderInfos.phtml','homePageTemplate.phtml');
    }


    public function purchaseOrderAction()
    {
        if(session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
            session_regenerate_id();
        }

        $billingInfos = $_SESSION['form']['orders']['billing'];
        $deliveryInfos = $_SESSION['form']['orders']['delivery'];


        $this -> viewData['billingInfos'] = $billingInfos;
        $this -> viewData['deliveryInfos'] = $deliveryInfos;
        
    

        $shoppingCartManager = new ShoppingCartManager();

        $idProducts = $shoppingCartManager ->getIds();

        $productsManager = new ProductsManager();

        $products = $productsManager ->getByIds($idProducts);

        $prixFinal = 0;

        foreach ($products as $key => $value)
        {

            $value['quantity'] = $shoppingCartManager ->getQuantityById($value['id']);

            $value['prixTotal'] = $value['priceTTC']*$value['quantity'];

            $prixFinal += $value['prixTotal'];
            $products[$key] = $value;

        }

        //var_dump($billingInfos,$deliveryInfos,$products,$prixFinal);

        $this -> viewData['products'] = $products;
        $this -> viewData['prixFinal'] = $prixFinal;



        $this -> generateView('purchaseOrder.phtml','homePageTemplate.phtml');
    }

    public function saveOrderInfosAction()
    {
        
        if(session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
            session_regenerate_id();
        }

        $billingInfos = $_SESSION['form']['orders']['billing'];
        $deliveryInfos = $_SESSION['form']['orders']['delivery'];
        $clientsManager = new ClientsManager();

        $clientInfo = $clientsManager ->getConnectionState();
        //var_dump($clientInfo);
        $clientId['id_customer'] = $clientInfo['id'];
        //var_dump($clientId);
        $ordersInfos = array_merge($billingInfos, $deliveryInfos, $clientId);
        
        //var_dump($ordersInfos);
        

        $shoppingCartManager = new ShoppingCartManager();

        $idProducts = $shoppingCartManager ->getIds();

        $productsManager = new ProductsManager();

        $products = $productsManager ->getByIds($idProducts);

        $prixFinal = 0;

        foreach ($products as $key => $value)
        {

            $value['quantity'] = $shoppingCartManager ->getQuantityById($value['id']);

            $value['prixTotal'] = $value['priceTTC']*$value['quantity'];

            $prixFinal += $value['prixTotal'];
            $products[$key] = $value;

        }

        $ordersManager = new OrdersManager();
        
        $id_Order = $ordersManager ->saveOrdersInfos($ordersInfos);
        
        //var_dump($id_Order);

        foreach ($products as $value)
        {
            $ordersManager ->saveOrderLinesInfos($value, $id_Order);
        }//Idéalement, il vaut mieux pas de mettre requete à l'intérieur de boucle.



        $categoriesManager = new CategoriesManager();

        $this -> viewData['categories'] = $categoriesManager ->getAll();

        $this -> generateView('orderSuccess.phtml','homePageTemplate.phtml');


    }

    
    
}