<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/articles" , name="admin_articles")
     */

    // Cette route permet à l'administrateur de gérer les articles
    // ArticleRepository contient une méthode qui permet de sélectionner l'ensemble des données d'une table : findAll()
    public function adminArticles(ArticleRepository $repo)
    {   
        // permet de manipuler la structure de la BDD
        // getclassMetadata() permet de récupérer les données liées à la table SQL => nom des colonnes, PK, FK...

        // getManager() est le gestionnaire d'entités de Doctrine. Il est responsable de l'enregistrement des objets et de leur récupération
        // dans la base de données
        $em = $this->getDoctrine()->getManager();

        // getFieldNames() permet de récupérer le nom des colonnes d'une table SQL à partir d'une entité.
        $colonnes = $em->getClassMetadata(Article::class)->getFieldNames();

        $articles = $repo->findAll();

        // dump($articles); renvoie l'array ci-dessous :

        // In AdminController.php line 31:
        // array:14 [▼
        // 0 => App\Entity\Article {#431 ▶}
        // 1 => App\Entity\Article {#660 ▶}
        // 2 => App\Entity\Article {#664 ▶}
        // 3 => App\Entity\Article {#668 ▶}
        // 4 => App\Entity\Article {#672 ▶}
        // 5 => App\Entity\Article {#704 ▶}
        // 6 => App\Entity\Article {#708 ▶}
        // 7 => App\Entity\Article {#712 ▶}
        // 8 => App\Entity\Article {#716 ▶}
        // 9 => App\Entity\Article {#658 ▶}
        // 10 => App\Entity\Article {#686 ▶}
        // 11 => App\Entity\Article {#692 ▶}
        // 12 => App\Entity\Article {#696 ▶}
        // 13 => App\Entity\Article {#700 ▶}

        // dump($colonnes); renvoie l'array ci-dessous :

        // array:5 [▼
        // 0 => "id"
        // 1 => "title"
        // 2 => "content"
        // 3 => "image"
        // 4 => "createdAt"
        // ]


        return $this->render('admin/admin_articles.html.twig' , [
            'articles' => $articles,
            'colonnes' => $colonnes

        ]);
    }

    /**
     * @Route("/admin/{id}/edit-article", name="admin_edit_article")
    */

    // Cette méthode permet de modifier un article depuis la page 'Gestion des articles' dans le back office
    public function editArticle(Article $article)
    {   
        dump($article);

        $form = $this->createForm(ArticleType::class, $article);

        return $this->render('admin/edit_article.html.twig', [
            'formEdit' => $form->createView()
        ]);
    }
}
