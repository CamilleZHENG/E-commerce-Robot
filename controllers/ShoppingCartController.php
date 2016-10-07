<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 26/07/16
 * Time: 11:28
 */
class ShoppingCartController extends Controller
{
    public function addProductAction()
    {
        
        $shoppingCartManager = new ShoppingCartManager();

        $shoppingCartManager ->addProduct($_GET['id']);
        
        //var_dump($_SESSION);
        
        header('Location:'.CLIENT_ROOT.'shoppingCart/show');
        //On va rappeler la fonctin showAction() dans le fichier shoppingCartController
        
        exit();

    }

    public function deletAction()
    {

        $shoppingCartManager = new ShoppingCartManager();

        $shoppingCartManager ->deletProduct($_GET['id']);

        //var_dump($_SESSION);

        header('Location:'.CLIENT_ROOT.'shoppingCart/show');
        //On va rappeler la fonctin showAction() dans le fichier shoppingCartController

        exit();
    }
    
    public function showAction()
    {
        $categoriesManager = new CategoriesManager();

        $this -> viewData['categories'] = $categoriesManager ->getAll();


        $shoppingCartManager = new ShoppingCartManager();
        
        $idProducts = $shoppingCartManager ->getIds();

        $productsManager = new ProductsManager();

        $products = $productsManager ->getByIds($idProducts);

        $prixFinal = 0;

        foreach ($products as $key => $value)
        {

            /*
            $products[$key]['quantity'] = $shoppingCartManager ->getQuantityById($products[$key]['id']);

            $products[$key]['prixTotal'] = $products[$key]['priceTTC']*$products[$key]['quantity'];

            $prixFinal += $products[$key]['prixTotal'];

            */

            $value['quantity'] = $shoppingCartManager ->getQuantityById($value['id']);

            $value['prixTotal'] = $value['priceTTC']*$value['quantity'];

            $prixFinal += $value['prixTotal'];
            $products[$key] = $value;
/* $value est utilisé que pendant le boucle, quand le boucle est terminé, $value disparait.
après donner la valeur à $value, il faut, avant la fin de chaque boucle, donner la valeur de $value à $products par:
$products[$key] = $value;
 * */

        }

        //var_dump($products);
        //var_dump($prixFinal);
        
        $this -> viewData['products'] = $products;
        $this -> viewData['prixFinal'] = $prixFinal;

        $clientsManager = new ClientsManager();

        $this -> viewData['connectedCustomer'] = $clientsManager ->getConnectionState();
        

        $this -> generateView('panier.phtml','homePageTemplate.phtml');
    }

    

    public function updateAction()
    {
        $shoppingCartManager = new ShoppingCartManager();
        
        if (array_key_exists('quantity', $_POST))
        {
            $quantity = $_POST['quantity'];

            foreach ($quantity as $key => $value)
            {
                if($value < 0)
                {
                    /*objectif: on va pas dire que c'est une erreur de saisir une quantité négative.
                    // Il faut juste ne pas tenir en compte cette modification vers une quantité négative :)
                    //throw new Exception('La quantité de produit ne peut pas être négative!');*/
                    $quantity[$key] = $shoppingCartManager ->getQuantityById($key);
                }
                elseif ($value == 0)
                {
                    unset($quantity[$key]);
                }
            }

            /*Méthodologie, on a récupéré un tableau $quantity suite à les saisies des clients
            les clés sont ID du produit, et valeur de chaque élément est le quantité saisie par l'utilisateur
             On va traité cette élément, et puis remplacer le tableau $_SESSION['shoppingCart']['products']
            (méthode update dans "ShoppingCartManager") pour actualiser le panier.
            Donc il suffit de modifier le tableau $quantity pour le mettre en état raisonnable
            et puis mettre à jour le le tableau $_SESSION['shoppingCart']['products'] avec le tableau $quantity.

            Dans le boucle "foreach", on vérifie la valeur de chaque élément:
            1. quand $value > 0 : on a besoin de rien faire, on garde cette valeur.
            2. quand $valeur = 0 : on supprime cet élément, dans le tableau $_SESSION, cette élément sera supprimé
                                   à travers de mise à jour. Sur l'affichage, cette ligne va disparaitre automatiquement.
            3. quand $valeur < 0 : on récupère  la valeur dans le tableau $_SESSION, on affecte cette valelur a $valeur.
                                c'est à dire, on va toujours garder la veleur pour cette id dans $_SESSION,
                                et la valeur saisiepar l'utilisateur sera pas prise en compte.
             */
            $shoppingCartManager->updateProducts($quantity);
        }
        //var_dump($quantity);

        header('Location:'.CLIENT_ROOT.'shoppingCart/show');
        //On va rappeler la fonctin showAction() dans le fichier shoppingCartController

        exit();

    }

    
    
    
    
}