<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    // public function load(ObjectManager $manager) 
    // //Injection de dépendance en argument de la méthode load(). La méthode Load dépend de l'interface ObjectManager qui a ses propres méthodes prédéfinies comme persist et flush.
    // {
    //     // Boucle for pour créer 10 objets Article donc 10 articles
    //     for($i = 1; $i <= 10; $i++) 
    //     {
    //         // Pour créer des articles, je passer par l'entité Article car elle reflète ma table Article. 
    //         // J'instancie un nouvel objet Article. On boucle 10 fois
    //         $article = new Article;

    //         // Pour avoir les titres, le contenu, les images et l'heure de chq article, on appelle les seters de l'objet Article
    //         // (car ses propriétés sont private) pour ajouter un titre, une image et une date à nos articles
    //         $article->setTitle("Titre de l'article n° $i")
    //                 ->setContent("<p> Contenu de l'article n° $i </p>")
    //                 ->setImage("https://picsum.photos/200")
    //                 // "\" permet de revenir au namespace global de php pour appeler l'entité DateTime
    //                 // DateTime est une classe prédéfinie en php qui permet de manipuler des objets date
    //                 ->setCreatedAt(new \DateTime());

    //         // Cette méthode de l'interface ObjectManager prépare la requête d'insertion et la stocke en mémoire.
    //         // $manager est un objet issu de l'interface Object manager et permet de manipuler les lignes de la BDD (INSERT, DELETE, UPDATE)
    //         $manager->persist($article);
            
    //     }
    //     // Cette méthode issue de l'interface ObjectManager libère/exécute la requête d'insertion en BDD
    //     $manager->flush();
    // }
    

    // Insertion de fixtures aléaotires liées dans notre 3 tables : article, comment, category
    public function load(ObjectManager $manager)
    {
        // Boucles imbriquées pour lier les catégories aux articles et pour que chaque article soit lié à plusieurs catégories.

        // J'appelle le namespace Factory et je génère des fixtures/fausses données en français avec la bibliothèque/ Class Faker (https://github.com/fzaninotto/Faker)
        $faker = \Faker\Factory::create('fr_FR');

        ////////////////////////////////////////////////////
        // 1. Création d'une boucle pour créer 3 catégories
        for($i = 1; $i <= 3; $i++)
        {   
            // On instancie l'entité Category afin d'insérer de nouvelles catégories dans la BDD
            $category = new Category;

            // On remplit l'objet Category grâce aux setters
            // Via l'objet $faker, on accède aux méthodes de la bibliothèque Faker qui permet de générer des phrases/images/paragaphes aléatoires
            // pour insérer des phrases (sentence()) et des paragraphes (paragraph()) aléatoires.
            // sentence() et paragraph() sont des méthodes issues de l'objet $faker
            $category->setTitle($faker->sentence());

            $category->setDescription($faker->paragraph());

            // On prépare l'insertion des catégories et on les garde en mémoire
            $manager->persist($category);

            ///////////////////////////////////////////////////////////
            // 2. Création entre 4 et 6 articles pour chaque catégorie
            // mt_rand() est une méthode php qui retourne des chiffres aléatoires entre 2 données.
            for($j = 1; $j <= mt_rand(4,6); $j++)
            {   
                // On instancie l'entité Article pour insérer de nouveaux articles dans la BDD
                $article = new Article;

                // On crée 5 paragraphes qui sont reliés entre eux grâce à join()
                // On séparer les paragraphes créés par des balises <p></p>
                $content = '<p>' . join($faker->paragraphs(5), '<p></p>') . '</p>';

                //On appelle les seters de l'objet Article
                $article->setTitle($faker->sentence())

                // On appelle $content qui crée plusieurs paragraphes et les lie entre eux.
                        ->setContent($content)

                // ImageUrl() est une fonction de la librairie Faker qui génère des URLs d'images aléatoires
                        ->setImage($faker->imageUrl())
                // dateTimeBetween() crée des dates d'articles aléatoires qui datent d'il y a - de 6 mois (entre janv 2020 et maintenant)
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))

                // setCategory est une méthode de l'entité Article qui lie les articles aux catégories
                // Ici, on relier les articles aux catégories que l'on vient de créer
                // On envoie les catégories nouvellement créées dans le seter.
                // Le seter permet de renseigner la categorie de l'article
                        ->setCategory($category);

                // Préparation de la requête SQL pour l'insertion des articles dans la table Article
                $manager->persist($article);

                /////////////////////////////////////////////////////////////
                // 3. Création entre 4 à 10 commentaires pour chaque article
                for($k = 1; $k <= mt_rand(4,10); $k++)
                {   
                    // On instancie l'entité Comment pour créer de nouveaux commentaires
                    $comment = new Comment;

                    $content = '<p>' . join($faker->paragraphs(2), '<p></p>') . '</p>';

                    // Différence/intervalle de temps entre la date de la création de l'article et la date d'aujourd'hui

                    // Objet contenant la date actuelle. Il faut faire en sorte d'avoir des dates de commentaires cohérentes,
                    // cad entre la date de création des articles et la date d'aujourd'hui
                    $now = new \DateTime;

                    // diff() permet de générer un timestamp en secondes entre 01/01/2020 et 29/06/2020.

                    $interval = $now->diff($article->getCreatedAt()); 
                    // représente le temps en timestamp entre la date de création de l'article et maintenant.
                    
                    // days() transforme un timestamp en jours.
                    $days = $interval->days; // nombre de jours entre la date de création de l'article et maintenant
                    $minimum = '-' . $days . 'days'; // ex : - 100 jours entre la date de la création de l'article et maintenant

                    // Création de noms d'auteurs aléatoires grâce à name()
                    $comment->setAuthor($faker->name()); // noms et prénoms aléatoires
                    // $content permet 2 générer 2 § distincts
                    $comment->setContent($content)
                    // dateTimeBetween() crée des dates entre la date de création de l'artilce et la date d'aujourd'hui
                            ->setCreatedAt($faker->dateTimeBetween($minimum))

                    // Relier les commentaires aux articles
                            ->setArticle($article);

                    // Préparation de la requête SQL pour l'insertion des commentaires dans la table Comment
                    $manager->persist($comment);


                }
            }
            // Exécution des requêtes SQL en BDD
            $manager->flush();
        }
    }
}
