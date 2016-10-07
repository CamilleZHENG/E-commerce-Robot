<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 29/07/16
 * Time: 15:21
 */
class OrdersManager extends Manager
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
    
    public function saveOrdersInfos(array $ordersInfos)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
        INSERT INTO
              Orders(
                    purchaseDate,
                    billingCivility,
                    billingFirstName,
                    billingLastName,
                    billingAddress,
                    billingZipCode,
                    billingCity,
                    billingCountry,
                    billingPhoneNumber,
                    deliveryCivility,
                    deliveryFirstName,
                    deliveryLastName,
                    deliveryAddress,
                    deliveryZipCode,
                    deliveryCity,
                    deliveryCountry,
                    deliveryPhoneNumber,
                    id_customer
              )
        VALUES (NOW(), '.implode(',',array_fill(0, count($ordersInfos),'?')).')
        ';
        //NOW() est pour obtenir la date de commande.
        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute(array_values($ordersInfos));

        $id = $connexionBDD->lastInsertId();
        //Pour obtenir le id pour les données que l'on vient d'enregistrer.

        return $id;
    }



    public function saveOrderLinesInfos(array $products, $id_Order)
    {

        //var_dump($products);

        $connexionBDD = $this->getDBConnection();
        $requete = '
        INSERT INTO
              OrderLines(
                    id_Order,
                    productName,
                    priceHT,
                    VATRate,
                    quantity,
                    id_Product
              )
        VALUE (?, ?, ?, ?, ?, ?)
        ';
        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute(
                [
                $id_Order,
                $products['name'],
                $products['priceHT'],
                $products['VATRate'],
                $products['quantity'],
                $products['id']
                ]);
        
        
    }

























    
    

}