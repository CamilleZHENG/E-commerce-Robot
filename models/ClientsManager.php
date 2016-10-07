<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 25/07/16
 * Time: 14:55
 */
class ClientsManager extends Manager
{

    public function addOneCompte(array $infoClient)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
        INSERT INTO
              Customers(
                  civility,
                  firstName,
                  lastName,
                  email,
                  passwordHash,
                  address,
                  zipCode,
                  city,
                  country,
                  phoneNumber
              )
        VALUES ('.implode(',',array_fill(0, count($infoClient),'?')).')
        ';
        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute(array_values($infoClient));
        //entre () de execute, on peut mettre que une table indicé (index array).
        //Pourant, $infoClient est un tableau associatif.
        //Il faut le convertir en tableau indicé en utilisant la fonction array_values();
    }



    public function getInfosCompte($mail)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
			    id,
				email,
				passwordHash,
				civility,
				firstName,
				lastName
			FROM
				Customers
			WHERE 
			    email = ?
		';


        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute([$mail]);

        $infosCompte = $resultatRequete->fetch();

        return $infosCompte;
    }
    
    public function getConnectionState()
    {
        if(session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
            session_regenerate_id();
        }

        if(!isset($_SESSION['userEnLine']['user']))
        {
            return null;
        }
        else
        {
            $infosCompte = $_SESSION['userEnLine']['user'];
            
            return $infosCompte;
        }
    }
    
    
    
    
    
    
    
    
    
    


}