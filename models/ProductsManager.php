<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 21/07/16
 * Time: 15:18
 */
class ProductsManager extends Manager
{

    public function getAll($page =1)//1 est argument par défaut
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				id,
				name,
				description,
				priceHT,
				VATRate,
				quantityInStock,
				imagePath,
				ROUND(priceHT * (1 + VATRate / 100), 2) AS priceTTC
			FROM
				Products
			ORDER BY
			    name
			LIMIT
			    ?, ?
		';

        $resultatRequete = $connexionBDD -> prepare($requete);
        
        $resultatRequete ->bindValue(1,($page -1 )* ProductsController::NUMBER_PER_PAGE, PDO::PARAM_INT);
        $resultatRequete ->bindValue(2, ProductsController::NUMBER_PER_PAGE, PDO::PARAM_INT);

        $resultatRequete ->execute();

        $products = $resultatRequete->fetchAll();

        return $products;

    }

    public function getByIds(array $ids)//il faut préciser que $id est un tableau
    {
        if(count($ids)<1)
        {
            return [];
        }

        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				id,
				name,
				priceHT,
				VATRate,
				imagePath,
				ROUND(priceHT * (1 + VATRate / 100), 2) AS priceTTC
			FROM
				Products
			WHERE 
			    id IN ('.implode(',',array_fill(0, count($ids),'?')).')
			ORDER BY 
			    name
		';

        //array_fill(0, count($ids),'?');On remplaceles élément du tableau par string, ex:'?'

        $resultatRequete = $connexionBDD -> prepare($requete);

        $resultatRequete ->execute($ids);

        $products = $resultatRequete->fetchAll();

        return $products;

    }





    public function getCountAll()
    {

        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				COUNT(id)
			FROM
				Products
		';

        $resultatRequete = $connexionBDD -> query($requete);

        $num = $resultatRequete->fetchColumn();
        //	Transmission du nombre de produits

        return $num;
    }

    public function getCountByCategory($idCategory)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				COUNT(id)
			FROM
				Products
			WHERE 
			    id = ?
		';

        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute([$idCategory]);

        $num = $resultatRequete->fetchColumn();
        
        return $num;
        
    }
    
    public function getCountBySearch($productName)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				COUNT(id)
			FROM
				Products
			WHERE
			    name LIKE CONCAT(\'%\', ?, \'%\')
		';

        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute([$productName]);

        $num = $resultatRequete->fetchColumn();

        return $num;
        
    }
    




    
    
    public function getOne($id)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				name,
				description,
				priceHT,
				VATRate,
				quantityInStock,
				imagePath,
				ROUND(priceHT * (1 + VATRate / 100), 2) AS priceTTC
			FROM
				Products
			WHERE
			    id = ?
		';
/*
        $resultatRequete = $connexionBDD -> query($requete);
        $product = $resultatRequete->fetch();
*/

        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute([$id]);

        $product = $resultatRequete->fetch();

        return $product;
    }


    public function getAllProductsOfCategory($id, $page = 1)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				name,
				description,
				priceHT,
				VATRate,
				quantityInStock,
				imagePath,
				ROUND(priceHT * (1 + VATRate / 100), 2) AS priceTTC
			FROM
				Products
			WHERE
			    id_Category = ?
			ORDER BY
			    name
			LIMIT
			    ?, ?
			
		';

        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete -> bindValue(1, $id, PDO::PARAM_INT);
        $resultatRequete -> bindValue(2,($page -1 )* ProductsController::NUMBER_PER_PAGE, PDO::PARAM_INT);
        $resultatRequete -> bindValue(3, ProductsController::NUMBER_PER_PAGE, PDO::PARAM_INT);

        $resultatRequete ->execute();

        $allProductsOfCategory = $resultatRequete->fetchAll();

        return $allProductsOfCategory;
    }
/*ma méthode: la différence avec la correction de Damin est la ligne:
  name LIKE "%"?"%" -> dans le code de SQL, on n'a pas de contaténation avec ".", on faut donc "%"?"%",
pas de "." dedans.
 $resultatRequete ->execute([$productName]);



    public function getProductResearched($productName)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				id,
				name,
				description,
				priceHT,
				VATRate,
				quantityInStock	
			FROM
				Products
			WHERE
			    name LIKE "%"?"%"
		';

        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute([$productName]);

        $productResearched = $resultatRequete->fetchAll();

        return $productResearched;
    }
*/
    /*
    //Méthode 1 de Damien : à l'étape de "execute", on met "%": execute(['%'.$productName.'%']);
    public function getProductResearched($productName, $page = 1)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				id,
				name,
				description,
				priceHT,
				VATRate,
				quantityInStock,
				imagePath,
			  	ROUND(priceHT * (1 + VATRate / 100), 2) AS priceTTC
			FROM
				Products
			WHERE
			    name LIKE ?
		';

        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute(['%'.$productName.'%']);

        $productResearched = $resultatRequete->fetchAll();

        return $productResearched;
    }
    */
//Méthode 2 de Damien. Dans requete sql, derrière WHERE, on met % : name LIKE CONCAT('%', ?, '%')
    public function getProductResearched($productName, $page = 1)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				id,
				name,
				description,
				priceHT,
				VATRate,
				quantityInStock,
				imagePath,
			  	ROUND(priceHT * (1 + VATRate / 100), 2) AS priceTTC
			FROM
				Products
			WHERE
			    name LIKE CONCAT(\'%\', ?, \'%\')
			ORDER BY
			    name
			LIMIT
			    ?, ?
		';

        $resultatRequete = $connexionBDD ->prepare($requete);




        $resultatRequete -> bindValue(1, $productName, PDO::PARAM_INT);
        $resultatRequete -> bindValue(2,($page -1 )* ProductsController::NUMBER_PER_PAGE, PDO::PARAM_INT);
        $resultatRequete -> bindValue(3, ProductsController::NUMBER_PER_PAGE, PDO::PARAM_INT);

        $resultatRequete ->execute();

        $productResearched = $resultatRequete->fetchAll();

        return $productResearched;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    

}