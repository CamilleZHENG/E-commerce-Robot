<?php
/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 20/07/16
 * Time: 10:24
 */
/*
include 'Manager.php';
include 'ElevesManager.php';
include 'MatiereManager.php';
*/

header('Content-Type: text/html; charset=utf-8');

define('SERVER_ROOT',__DIR__.'/');//pour aider définir le chemin serveur de dossier
define('CLIENT_ROOT', str_replace($_SERVER['DOCUMENT_ROOT'], '',__DIR__.'/'));//chemin, à 3WA
//define('CLIENT_ROOT', '/2016-07-28-projet-pro-e-commerce/');//chemin utilisé à la maison
//var_dump(__DIR__, $_SERVER, CLIENT_ROOT);


spl_autoload_register(function($className)
{
    //var_dump($className);

    if(file_exists('core/'.$className.'.php'))
    {
        include SERVER_ROOT.'core/'.$className.'.php';
    }
    elseif(file_exists('models/'.$className.'.php'))
    {
        include SERVER_ROOT.'models/'.$className.'.php';
    }
    elseif(file_exists('controllers/'.$className.'.php'))
    {
        include SERVER_ROOT.'controllers/'.$className.'.php';
    }

});



//Cela est pour tester si tout fonctionne.
/*
$elevesManager = new ElevesManager();

var_dump($elevesManager);

$eleves = $elevesManager ->getAll();

var_dump($eleves);


$matiereManager = new MatiereManager();

var_dump($matiereManager);

$matieres = $matiereManager ->getAll();

var_dump($matieres);

*/

/*
$elevesController = new ElevesController();
$elevesController ->showAllAction();

$matieresController = new MatieresController();
$matieresController ->showAllAction();
*/

/*
$controllerName = ucfirst($_GET['controller']).'Controller';
//$_GET['controller'] nous permet de recuperer string "eleves"
//ucfist + 'Controller' nous permet d'obtenir le nom de classe "ElevesManager"
$actionName = $_GET['action'].'Action';
//$_GET['action'] nous permet de recuperer le nom de action "showAll"
//On rajoute 'Action' a la fin pour avoir le nom de fonction entiere "showAllAction"
*/
/*dans mthode GET: /?controller=eleves&action=showAll
$controllerName = $_GET['controller'];  -> on récupère le "eleves"
$actionName = $_GET['action'];
Si on veut "matieres":
dans mthode GET: /?controller=matiere&action=showAll
*/
//var_dump($controllerName,$actionName);
//Pour tester cela, à la fin de l'adresse URL, on met "?controller=eleves&action=showAll"

/*
$controller = new $controllerName;

$controller -> $actionName();
*/

try{

    //Pour la sécurité et avoir plus de controle:
    if(array_key_exists('controller',$_GET))//Vérifier d'abord s'il existe
    {
        $controllerName = ucfirst($_GET['controller']).'Controller';

            if(class_exists($controllerName))
                //Si cette classe existe(ex: elevesController, ex;matiereController), on continue.
                //Sinon quand cette classe n'existe pas, on passera dans else
            {

                if(array_key_exists('action',$_GET))//Verifier s'il la variable pour action existe.
                {
                    $controller = new $controllerName;

                    $actionName = $_GET['action'].'Action';

                    if(method_exists($controllerName,$actionName))
                    {
                        $controller -> $actionName();
                    }
                    else
                    {


                        throw new Exception('L\'action <strong>'.$actionName.'</strong> n\'existe pas dans le contrôleur <strong>'.$controllerName.'</strong> !');
                        //action que l'on cherche n'existe pas

                    }

                }
                else
                {
                    throw new Exception('Aucune action n\'est fournie !');
                    //aucune action existe
                }


            }
            else
            {
                throw new Exception('Le contrôleur <strong>'.$controllerName.'</strong> n\'existe pas !');

                //controller que l'on cherche n'existe pas

            }



    }
    else
    {
        throw new Exception('Aucun contrôleur n\'est fourni !');
        //aucune controller

    }

}

catch(Exception $exception)//on capturer
{


    echo '<h1>Erreur</h1>';
    echo '<h2>Message</h2>';
    echo $exception->getMessage();
    echo '<h2>Fichier et ligne</h2>';
    echo $exception->getFile().' : '.$exception->getLine();
    echo '<h2>Informations complémentaires</h2>';

    //var_dump($exception);

}
