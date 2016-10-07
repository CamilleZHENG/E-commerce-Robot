<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 21/07/16
 * Time: 15:17
 */
class CategoriesManager extends Manager
{

    public function getAll()
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
			SELECT
				Categories.id,
				Categories.name,
				COUNT(Categories.id) AS numProducts
			FROM
				Categories
			INNER JOIN
			    Products
			ON 
			    Categories.id = Products.id_Category
			GROUP BY
			    Categories.id
		';

        $resultatRequete = $connexionBDD -> query($requete);


        $categories = $resultatRequete->fetchAll();

        return $categories;

    }

    public function getOne($id)
    {
        $connexionBDD = $this->getDBConnection();

        $requete = '
            SELECT
				Categories.id,
				Categories.name,
				COUNT(Categories.id) AS numProducts
			FROM
				Categories
			INNER JOIN
			    Products
			ON 
			    Categories.id = Products.id_Category
			WHERE
			    Categories.id = ?
			GROUP BY
			    Categories.id
		';


        $resultatRequete = $connexionBDD ->prepare($requete);

        $resultatRequete ->execute([$id]);

        $category = $resultatRequete->fetch();

        return $category;
    }



}