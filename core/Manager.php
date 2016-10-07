<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 20/07/16
 * Time: 10:08
 */
abstract class Manager
{
    //Création de constante pour utiliser que les constante après, cela simplifie le cas.
    const DSN = 'mysql:dbname=Shop;host=localhost;charset=utf8';
    const USER_NAME = 'root';
    const PASSWORD = 'troiswa';

    private static $PDOInstance;//on crée un propriété

    protected function getDBConnection()
    {







        if(self::$PDOInstance ==null)//La connection est faite dans la condition de if
        {
            self::$PDOInstance = new PDO
            (
                self::DSN,
                self::USER_NAME,
                self::PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            //var_dump('Connexion');


        }



        return self::$PDOInstance;


    }


}
