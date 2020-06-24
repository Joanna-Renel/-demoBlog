<?php

namespace App\Controller;


use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
        /*

        Symfony fonctionne toujours ave un système de routage.
        La méthode d'un controlleur sera exécutée en fonction de la route transmise dans l'URL (ici : http://localhost:8000/blog)
        
        Ex : si nous envoyons la route '/blog' dans l'url, on fait appel au controlleur "BlogController"
        et exécute la méthode 'index()'. Cette méthode renvoie un template sur le navigateur (néthode render())


        Symfony va récupérer cette route dans l'URL et va exécuter la méthode index()
        qui retourne le template index.html.twig qui est responsable de l'affichage du blog
        */
                           
    // Symfony interprète les commentaires comme celui ci-dessous (@Route)                     
    // Les annotations doivent toujours contenir 4 astérisques pour être interpétées par Symfony
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
        /*
            Un des principes de base de Symfony est l'injection de dépendances.
            Par exemple, ici, dans le cas de la méthode index(), on a besoin de la classe ArticleRepository pour fonctionner correctement.
            cad que la méthode index() dépend de la classe ArticleRepository.
            Donc ici, on injecte une dépendance en argument de la méthode index(), on impose un objet issu de la classe ArticleRepository.
            Du coup, plus besoin de faire appel à Doctrine (getDoctrine())
            $repo est un objet issu de la classe ArticleRepository et nous avons accès à toute les méthodes issues de cette classe
            Les méthodes sont moins chargé et c'est plus simple d'utilisation.

        */

        // index() possède la route '/blog' qui affiche tous les articles. on y déclare la requête de sélection de tous les articles
        // INJECTION DE DÉPENDANCE = On appelle la classe ArticleRepository en argument pour avoir accès à toutes les méthodes définies dedans
        // car la méthode index() dépend de cette classe. On impose en argument un objet Repository issu de la classe ArticleRepository.
    {
        // 1. appel de doctrine
        // 2. sélection en BDD

        /*
            Pour sélectionner des données en BDD, on a besoin de la classe Repository de la classe Article.
            Une classe Repository permet uniquement de sélectionner des données en BDD (requête SQL SELECT);
            On a besoin de l'ORM Doctrine pour faire la relation entre la BDD et le controller avec getDoctrine().
            getRepository() est une méthode issue de l'objet Doctrine qui permet d'importer une classe Repository (SELECT).

            $repo est un objet issu de la classe ArticleRepository qui contient des méthodes prédéfinies par Symfony
            et qui permettent de sélectionner des données en BDD (find(), findBy(), findOneBy(), findAll())

            findAll() est une méthode issue de la classe ArticleRepository permettant de sélectionner l'ensemble de la table SQL, donc ici la table Article.
        */

        // $repo-> cette variable de réception est un objet de la classe ArticleRepository qui permet de sélectionner en BDD grâce aux méthodes de cette classe
        // $this-> j'accède aux méthodes de l'objet
        // getDoctrine fait la relation avec la BDD
        // getRepository importe le repository d'une classe ("va chercher le repository de la classe Article et retourne un objet de la classe repository)
        // J'ai donc accès à toutes les méthodes de cet objet
        // Penser à importer la classe Article (Use App\entity\Article) clic droit-> import class OU ctrl + alt + i

        // Dans l'argument d'index, l'argument $repo accède à toutes les méthodes de la classe ArticleRepository
        // $repo = $this->getDoctrine()->getRepository(Article::class); // va chercher la classe articleRepository

        //
        $articles = $repo->findAll(); // équivalent à SELECT * FROM article + fetchAll

        // dump() est l'outil de debugage de Symphony équivalent var_dump()
        // et fait un fetchAll
        dump($articles);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController', // le template index.html.twig sur le navigateur

            // j'envoie tous les articles sélectionnés sur le template
            // On réceptionne les articles sur le template index.html.twig
            // On traitera ensuite les infos sur le template avec twig
            'articles' => $articles // on envoie sur le template 'index.html.twig' les articles sélectionnés en BDD ($articles)
            // que nous allons traiter avec le langage TWIG sur le template
        ]);
    }

    
    /**
     * @Route ("/", name="home")
     */

        // argument 1 : point d'entrée du site, (de l'application)
        // argument 2 : nom de la route
    public function home() 
        // cette méthode envoie le rendu sur le navigateur avec le template 'home.html.twig"
        // et permet d'envoyer les données au navigateur provenant de la BDD
    {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue sur le blog Symfony",
            'age' => 25
        ]);
    }

    // On créer une nouvelle route pour créer un formulaire qui insèrera de nouveaux articles et les modifiera
    /**
     * @Route ("/blog/new", name="blog_create")
     */

    public function create()
    {  
        // Cette méthode renvoit, sur le navigateur, le template create.html.twig qui sera responsable de l'affichage du formulaire.
        return $this->render('blog/create.html.twig');
    }


    /**
     * // Route paramétrée qui attend l'id des articles de la BDD en paramètre
     * @Route("/blog/{id}", name="blog_show")
     */

        // Pour chaque template, on a une route et une méthode précises
        // show() est une méthode permettant de voir le détail d'un article

    public function show(ArticleRepository $repo, $id)

        /*
            show(ArticleRepository $repo) --> $repo est une variable de réception déclarée directement en argument de la fonction show().
            On peut la nommer à souhait. Elle réceptionne un objet issu de la classe ArticleRepository et sera appelée l146 pour appeler la méthode find()

            Pour sélectionner 1 article en BBD, cad voir le détail de l'article, on utilise le principe de route paramétrée /blog/{id}.
            Notre route attend un paramètre de type {id}, donc de l'id d'un article qui est stocké en BDD.
            Lorsque nous transmettons une route dans l'URL, par ex "/blog/9, on envoie un id connu dans l'URL
            et Symfony va automatiquement récupérer ce paramètre pour le transmettre en argument de la méthode show($id).

            Cela veut dire que nous avons accès à l'id à l'intérieur de la méthode show().
            Le but est de sélectionner les données en BDD de l'id récupéré en paramètre.
            Nous avons besoin, pour cela, de la classe ArticleRepository afin de pouvoir sélectionner en BDD.
            La méthode find() est issue de la classe ArticleRepository et permet de selectionner des données en BDD avec un argument de type {id}
            DOCTRINE fait ensuite tout le travail pour nous, c'est à dire qu'elle recupère la requete de selection pour l'ex

        */

        // On ajoute en argument en paramètre une variable de réception $id
        // Elle réceptionne un id qui sera stocké dans la route. Ex: /blog/{id 1} pour l'article 1
        // Symfony ira chercher l'article 1 grâce à son id1.
    {   
        // Pour faire des requêtes de sélection, on doit passer par un repository
        // On appelle doctrine : va chercher la class repository de la class Article (penser au Use Article pour importer la classe)
        // Il récupère le contenue de la class qui sera stocké dans $repo
        // Dans cette class, on accède à toutes les méthodes déclarées dans cette class dont find()
        // $repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id); // id 1 en argument de la méthode pour récupérer l'article ayant l'id 1
        // On récupère ici l'id qui a été envoyé dans l'URL blog/{id}

        dump($article);
        // Envoie  et l'article récupéré en BDD.

        // La méthode render() attend 2 arguments
        //  1. le template qui doit être renvoyé sur le navigateur
        //  2. tableau array qui contient toutes les options à envoyer avec le template

        // On ajoute à la méthode render() un array avec un indice 'article' qui deviendra une variable dans show.html.twig
        return $this->render('blog/show.html.twig', [
            'article' => $article
        // L'article sélectionné en BDD est envoyé dans le template show.html.twig grâce à la méthode render().
        // On envoie avec le template 'show.html.twig' l'article sélectionné en BDD
        ]);

        // Arguments render() = ('template_à_envoyer', 'ARRAY options')
    }

    
}
