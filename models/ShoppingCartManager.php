<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 26/07/16
 * Time: 11:30
 */
class ShoppingCartManager extends Manager
{


    /*Organisation de $_SESSION:
    organiser ce tableau à plusieurs niveaux:


    $_SESSION['shoppingCart']['products']['troisème clé est la valeur de id']
    = la valeur de l'élément correspand la quantité
    ex:  $_SESSION['shoppingCart']['products'][5] = 2
     on met 2 produit de id 5 dans mon panier
     *
     * */

    public function __construct()
    {
        //Avant démarrer une session, il faut vérifier s'il existe déjà,
        // on le démarre que quand il existe pas encore

        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }

        /*Pour la sécurité de session:
        session est une variable spéciale
        il stocke qch sur notre ordi, quand on dit que l'on démarre le session,

        à chaque fois on se connecte à session,
        on change identifiant

        */
        session_regenerate_id();

        //si ce tableau $_SESSION['shoppingCart']['products'] n'existe pas encore, on le crée

        if (!isset($_SESSION['shoppingCart']['products']))
        {
            $_SESSION['shoppingCart']['products'] = [];
        }
    }


    public function addProduct($id)
    {

        if(array_key_exists($id, $_SESSION['shoppingCart']['products']))
        {

            $_SESSION['shoppingCart']['products'][$id]++;

        }
        else
        {
            $_SESSION['shoppingCart']['products'][$id] = 1;
        }

    }

    public function deletProduct($id)
    {
/*
 * //pour réduire la quantité de produit
        if(array_key_exists($id, $_SESSION['shoppingCart']['products']))
        {

            if( $_SESSION['shoppingCart']['products'][$id] == 1)
            {
                unset($_SESSION['shoppingCart']['products'][$id]);

            }
            else
            {
                $_SESSION['shoppingCart']['products'][$id]--;
            }

        }
        else
        {
            throw new Exception('Ce produit n\'existe pas!');
        }
        */
            unset($_SESSION['shoppingCart']['products'][$id]);

            //unset($_SESSION['shoppingCart']['products']); -> on va vider le panier
    }

    public function updateProducts(array $id_quantity)
    {
        $_SESSION['shoppingCart']['products'] = $id_quantity;
    }

    public function getIds()
    {
        $Ids = array_keys($_SESSION['shoppingCart']['products']);

        return $Ids;
    }
    
    public function getQuantityById($id)
    {
        $quantity = $_SESSION['shoppingCart']['products'][$id];
        
        return $quantity;
    }


}