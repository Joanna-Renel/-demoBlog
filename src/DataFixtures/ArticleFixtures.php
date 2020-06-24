<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager) 
    //Injection de dépendance en argument de la méthode load(). La méthode Load dépend de l'interface ObjectManager qui a ses propres méthodes prédéfinies comme persist et flush.
    {
        // Boucle for pour créer 10 objets Article donc 10 articles
        for($i = 1; $i <= 10; $i++) 
        {
            // Pour créer des articles, je passer par l'entité Article car elle reflète ma table Article. 
            // J'instancie un nouvel objet Article. On boucle 10 fois
            $article = new Article;

            // Pour avoir les titres, le contenu, les images et l'heure de chq article, on appelle les seters de l'objet Article
            // (car ses propriétés sont private) pour ajouter un titre, une image et une date à nos articles
            $article->setTitle("Titre de l'article n° $i")
                    ->setContent("<p> Contenu de l'article n° $i </p>")
                    ->setImage("https://picsum.photos/200")
                    // "\" permet de revenir au namespace global de php pour appeler l'entité DateTime
                    // DateTime est une classe prédéfinie en php qui permet de manipuler des objets date
                    ->setCreatedAt(new \DateTime());

            // Cette méthode de l'interface ObjectManager prépare la requête d'insertion et la stocke en mémoire.
            // $manager est un objet issu de l'interface Object manager et permet de manipuler les lignes de la BDD (INSERT, DELETE, UPDATE)
            $manager->persist($article);
            
        }
        // Cette méthode issue de l'interface ObjectManager libère/exécute la requête d'insertion en BDD
        $manager->flush();
    }
}
