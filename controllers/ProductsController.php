<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 22/07/16
 * Time: 10:35
 */
class ProductsController extends Controller
{
    const NUMBER_PER_PAGE = 1;

    public function __construct()
    {
        $categoriesManager = new CategoriesManager();

        $this -> viewData['categories'] = $categoriesManager ->getAll();
        //Cela est pour que l'on répète pas ces mêmes codes dans chaque fonction au-dessous.
        $clientsManager = new ClientsManager();

        $this -> viewData['connectedCustomer'] = $clientsManager ->getConnectionState();

    }

    public function showAllAction()
    {
     /*Cette partie de codes sont déplacé dans "constructeur", on a donc pas besoin de répéter chaque fois.
        $categoriesManager = new CategoriesManager();//Pour avoir bases de donées

        $this -> viewData['categories'] = $categoriesManager ->getAll();
        //on met la bdd dans un variable 'categorie'.
        //cette variable 'categorie' permet de HTML
        // (example.phtml dans dossier "views", dans cet example c'est robotHomepage.phtml)

        //var_dump($this -> viewData['categories']);
*/
        $productsManager = new ProductsManager();

        
        $numProducts = $productsManager -> getCountAll();

        $this -> viewData['numProducts'] = $numProducts;

        $requestedPage =  (array_key_exists('requestedPage',$_GET) ? $_GET['requestedPage'] : 1);
//Pour dire si le key 'actuelPage' existe, on affecte $_GET['actuelPage'] à $requestedPage

        $this -> viewData['requestedPage'] = $requestedPage;


        $numPage = ceil($numProducts/self::NUMBER_PER_PAGE);

        $this -> viewData['numPage'] = $numPage;

        if($requestedPage < 1 OR $requestedPage > $numPage)
        {
            throw new Exception('La page demandée n\'existe pas ! ');
        }
        //var_dump($this->viewData);
        
        $this -> viewData['products'] = $productsManager ->getAll($requestedPage);

        //var_dump($this -> viewData['products']);

        $this -> generateView('robotHomepage.phtml','homePageTemplate.phtml');
        // , 'homeTemplate.phtml' , 'homeTemplate.phtml'
        //Deux paramètres à l'intérieur de ():
        //1. fichier phtml (issue de dossier "vue") que l'on veut mettre qui crée un "$content" concerne le BDD
        //2. fichier tamplate (issue de dossier "vue" -> "template" ), si on met pas le fichier que l'on a paersonalisé, ex: homeTemplate.phtml,
        //c'est à dire, on met pas ce 2ème paramètre. C'est le fichier default.phtml qui est ce 2ème paramètre.
        //le fichier dans le dossier view (dans le model: example)
        
    }
    

    public function showOneProductAction(){

        if(array_key_exists('id',$_GET )){
            $productId = $_GET['id'];
        }
        else//si le clé 'id'n'existe pas.
        {
            throw new Exception('Pas d\'id');
        }

        $productsManager = new ProductsManager();

        $this -> viewData['product'] = $productsManager ->getOne($productId);

        if ($this -> viewData['product'] ===false)
            //On a récupéré un id, mais il existe pas un produit correspondant ce produit
        {
            throw new Exception('Ce produit n\'existe pas');
        }

        $this -> generateView('oneProduct.phtml','homePageTemplate.phtml');
        
    }


    public function showAllProductOfOneCateAction(){

        if(array_key_exists('id',$_GET )){
            $categoryId = $_GET['id'];
        }
        else//si le clé 'id'n'existe pas.
        {
            throw new Exception('Pas d\'id de category');
        }


        $categoriesManager = new CategoriesManager();//Pour avoir bases de donées

        $this -> viewData['category'] = $categoriesManager ->getOne($categoryId);

        if ($this -> viewData['category'] === false)
            //On a récupéré un id, mais il existe pas une categorie correspondant ce produit
        {
            throw new Exception('Cette category n\'existe pas');
        }

        $productsManager = new ProductsManager();
        

        $numProducts = $productsManager -> getCountByCategory($categoryId);

        $this -> viewData['numProducts'] = $numProducts;
        

        $requestedPage =  (array_key_exists('requestedPage',$_GET) ? $_GET['requestedPage'] : 1);

        $this -> viewData['requestedPage'] = $requestedPage;


        $numPage = ceil($numProducts/self::NUMBER_PER_PAGE);

        $this -> viewData['numPage'] = $numPage;

        if($requestedPage < 1 OR $requestedPage > $numPage)
        {
            throw new Exception('La page demandée n\'existe pas ! ');
        }
        
        
        

        $this -> viewData['allProductsOfCategory'] = $productsManager ->getAllProductsOfCategory($categoryId, $requestedPage);


        $this -> generateView('oneCategory.phtml','homePageTemplate.phtml');
    }


    public function showResearchResultAction()
    {
        /* pour le barre de recerche, ser sert normalement à la méthode $_GET.
        $_POST n'est pas faux, mais on peut pas partager le lien qui affiche le résultat de recherche.
            if(array_key_exists('research', $_POST))
            {
                $research = $_POST['research'];
                //var_dump($research);
            }
            else
            {
                throw new Exception('Vous avez pas effectué votre recherche');
            }
        */
        if(!array_key_exists('research', $_GET))
        {
            throw new Exception('Vous avez pas effectué votre recherche');
        }

        $research = trim($_GET['research']); //pour filtrer les espaces saisie par l'utilisateur

        if(empty($research))
        {
            throw new Exception('Recherche vide');
        }

        $this -> viewData['search'] = $research;
        
        $productsManager = new ProductsManager();
        

        $numProducts = $productsManager -> getCountBySearch($research);

        $this -> viewData['numProducts'] = $numProducts;
        

        $requestedPage =  (array_key_exists('requestedPage',$_GET) ? $_GET['requestedPage'] : 1);


        $this -> viewData['requestedPage'] = $requestedPage;


        $numPage = ceil($numProducts/self::NUMBER_PER_PAGE);

        $this -> viewData['numPage'] = $numPage;

        if($requestedPage < 1 OR $requestedPage > $numPage)
        {
            throw new Exception('La page demandée n\'existe pas ! ');
        }
        

        $this -> viewData['productResearched'] = $productsManager ->getProductResearched($research, $requestedPage);

        //var_dump($this -> viewData['productResearched']);

        /*if ($this -> viewData['productResearched'] ===false)
        {
            throw new Exception('Ce produit n\'existe pas');
        }
        Ceci n'est pas nécessaire, parce que dans "researchResult.phtml", 
        on a déjà "if(count($allProductsOfCategory) > 0):" pour vérifier si ce produit existe.
        */

        $this -> generateView('researchResult.phtml','homePageTemplate.phtml');
        
    }
    

}