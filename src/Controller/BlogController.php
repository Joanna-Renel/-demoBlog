<?php

namespace App\Controller;


use DateTime;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            Donc ici, on injecte/importe une dépendance en argument de la méthode index(), on impose un objet issu de la classe ArticleRepository.
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
        // renvoie un tableau multidimensionnel

        // dump() est l'outil de debugage de Symphony équivalent var_dump()
        // et fait un fetchAll
        dump($articles);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController', // le template index.html.twig renvoyé sur le navigateur en suivant la route /blog

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
            'title' => "Bienvenue sur mon blog Symfony",
            'age' => 25
        ]);
    }

    // '/blog/new' crée une nouvelle route pour créer un formulaire qui insèrera de nouveaux articles et les modifiera
    // Quand aucun id n'est détecté dans l'URL par Symfony, il crée un nouvel article.
    /**
     * @Route ("/blog/new", name="blog_create")
     * @Route ("/blog/{id}/edit", name="blog_edit")
     *
     */
    // La 2e route '/blog/{id}/edit' servira à modifier un article en fonction de son id.
    // L'injection de dépendance Article $article permet à SYMFONY de récupérer l'id de l'article à modifier dans la BDD
    // '$article = null' car l'article doit être vide pour permettre, soit de créer un article (avec ses geters et seters) soit de le modifier
    // En injectant 'Article $article = null' en paramètre de la méthode create(), on indique que l'article est null par défaut et n'aura de valeur
    // que s'il existe déjà en BDD et que son id figure dans l'URL. 

    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)

        /*
            La classe Request est une classe prédéfinie en Symfony qui stocke toutes les données véhiculées par les Superglobales ($_POST,
            $_COOKIE, $_GET...)
            Nous avons accès aux données saisies dans le formulaire via l'objet $request.
            La propriété $request->request représente la superglobale $_POST. Les données saisies dans le formulaire sont accessibles via cette propriété.
            Pour insérer un nouvel article, nous devons instancier la class/entité Article pour obtenir un objet Article vide
            et permettre de renseigner tous les setteurs de l'objet $article.

            EntityManagerInterface est une interface prédéfinie en Symfony qui permet de manipuler les lignes de la BDD (INSERT, UPDATE, DELETE).
            Elle possède des méthodes permettant de préprarer et d'exécuter les requêtes SQL (persist () | flush ()).

            persist() est une méthode issue de l'interface EntityManagerInterface qui permet de préparer et de stocker la requête SQL.
            flush() est une méthode issue de l'interface EntityManagerInterface qui permet de libérer et d'exécuter la requête SQL.

            redirectToRoute() est une méthode prédéfinie en Symfony qui permet de rediriger vers une route spécifique.
            Dans notre cas, on redirige après insertion vers la route 'blog_show' (détail de l'article que l'on vient d'insérer)
            et on transmet à la méthode l'id de l'article à envoyer dans l'URL.

            get() est une méthode de l'objet $request qui permet de récupérer les données saisies dans les différents attributs 'name' du formulaire.
            

        */

        // Pour récupérer les données saisies dans le formulaire "Créer un article", on utilise la class Request
        // La class Request est une class prédéfinie en Symfony et stocke les informations véhiculées par les super globales get et post.
        // La méthode create() dépend de la class Request (=Injection de dépendances). $request est un objet de la class Request.
        // $request récupère les données saisies dans le formulaire et crée un nouvel objet.
        

    {  
        dump($request);


        ///////////////////////////////////////////////////////////////////////////////////
        // 1ERE METHODE POUR CREER UN FORMULAIRE
        //////////////////////////////////////

        //Dans la console, request contient un sac de paramètres ou parameter bag.

        // Si $request contient des données, on génère une insertion.
        // Je pioche dans l'objet $request la propriété request qui contient les données du formulaire et la méthode count()
        // if ($request->request->count() > 0)
        // {   
        //     // INSTANCIATION D'UN OBJET ARTICLE VIDE
        //     $article = new Article; // pour créer un nouvel article, je dois passer par un nouvel article

        //     // Je pioche dans l'objet $request la propriété request qui contient les données du formulaire
        //     // et la méthode get() qui contient le titre, le contenu et l'image de l'article
        //     // $article contient les données saisies par le formulaire
        //     $article->setTitle($request->request->get('title')) // équivalent de $_POST['title'];
        //             ->setContent($request->request->get('content'))
        //             ->setImage($request->request->get('image'))
        //             ->setCreatedAt(new DateTime());

        //     // INSERTION DU NOUVEL ARTICLE CRÉÉ EN BDD
        //     // EntityManagerInterface est une autre class prédéfinie de Symfony qui contient des méthodes qui permettent de générer une requête d'insertion et d'insérer dans la BDD
        //     // $manager contient un objet issu de l'interface EntityManagerInterface

        //     // 1. persist() va permettre de prérarer la requête d'insertion. il récupère les geters (données injectées dans l'objet) et les envoie dans une requête SQL
        //     $manager->persist($article);
            
        //     // 2. flush() exécute la requête d'insertion et envoie les données en BDD
        //     $manager->flush();

        //     // AFFICHAGE DE L'OBJET ARTICLE EN CONSOLE AVEC LES DONNÉES SAISIES DANS LE FORMULAIRE
        //     dump($article);

        //     // REDIRECTION VERS L'ARTICLE NOUVELLEMENT CRÉÉ
        //     // Après l"insertion en BBD, on est redirigé vers la route qui affiche le détail d'un article.
        //     // redirectToRoute() redirige vers une route spécifique :
        //     //      argument 1 = nom de la route qui est paramétrée et qui attend un id
        //     //      argument 2 = id de l'article qui vient d'être créé
        //     // La route blog_show attend un id. On envoie donc l'id de l'article que l'on vient de créer.
        //     return $this->redirectToRoute('blog_show', [
        //         'id' => $article->getId()
        //     ]);
            
        // }
        // Cette méthode renvoie, sur le navigateur, le template create.html.twig qui sera responsable de l'affichage du formulaire.
        




        ////////////////////////////////////////////////////////////////////////////////////////////////
        // 2E METHODE POUR CREER UN FORMULAIRE
        //////////////////////////////////////


        // 1. On crée un nouvel article.
        // Si l'article n'existe pas et que l'URL ne contient l'id d'aucun article récupéré en BDD, on instancie la classe Article pour créer un nouvel article.

            /* Si l'article n'existe pas (car non défini et null), cela veut dire qu'aucun id n'a été transmis dans l'URL
            On entre aloirs dans la condition pour créer/insérer un nouvel article
            */
        if(!$article)
        {
            $article = new Article;

        }

            /* On importe la classe permettant de créer le formulaire d'ajout/modification d'article (ArticleType)
               On envoie, en 2e argument, l'objet $article pour spécifier que le formulaire est destiné à remplir l'objet $article. 
            */
        // createForm() permet d'appeler une classe de type formulaire en premier argument (ArticleType)
        // On précise l'objet à remplir en 2e argument.
        $form = $this->createForm(ArticleType::class, $article);
        
        // En remplissant l'objet, Symfony est capable de récupérer ce que contient cet objet et de les envoyer dans les champs
        // $article->setTitle('contenu bidon')
        //         ->setContent('article bidon');


        // createFormBuilder permet de générer un formulaire à partir d'une entité. On lui envoie en argument l'objet article pour lui préciser 
        // que le formulaire va servir à remplir l'objet article.

        // add() est une fonction de Symforny qui permet de créer un champ du formulaire

        // getForm() valide le formulaire et permettra d'avoir un rendu visuel sur le template
        // TextType est une classe prédéfinie qui permet d'ajouter un champ texte
        // On peut aussi ajouter des attributs
        // $form = $this->createFormBuilder($article)
         
        //                 // ->add('title', TextType::class, [
        //                 //     'attr' => [
        //                 //         'placeholder' => "Saisir le titre de l'article.",
        //                 //         'class' => "col-md-6 mx-auto"
        //                 //     ]
        //                 // ])

        //                 ->add('title')
        //                 ->add('content')
        //                 ->add('image')
        //                 ->getForm();

        /*handleRequest() est une méthode issue de l'objet $form et récupère les données du formulaire et remplit les setters à notre place.
        Elle remplit l'objet Article à notre place en faisant en injectant les données dans les setters :
        $article->setTitle($request->request->get('title')) // équivalent de $_POST['title'];
                    ->setContent($request->request->get('content'))
                    ->setImage($request->request->get('image'))
                    ->setCreatedAt(new DateTime());
        */

            /* La méthode handleRequest() permet de récupérer toutes les valeurs du formulaire contenu dans $request (équivalent à un $_POST)
            afin de les envoyer directement dans les setters de l'objet $article.
            */
        $form->handleRequest($request); // = $form->handleRequest("données du formulaire")

        /* Si le formulaire a bien été soumis quand on a cliqué sur 'submit', et que tout est bien validé, cad que chaque valeur
         du formulaire a bien été envoyée dans les bons setters, alors on entre dans la condition if. 
        */
        // Si le formulaire est soumis et que ces champs contiennent des données valides (que les setters ont bien été définis)
        if($form->isSubmitted() && $form->isValid())
        {   
            /*
             Afin de garder la date d'origine de création de l'article, en cas de modification, on contrôle l'existance de l'article :
                1. Si l'id de l'article n'est pas défini, cela veut dire que c'est un nouvel article, donc une insertion, alors on envoie
             
            */
            // Si l'article ne possède pas d'id, j'appelle le seter et je crée un nouvel objet DateTime pour créer une nouvelle date.
            // Si l'article possède un id, la condition ne s'applique pas et on conserve la date de publication d'origine.
            if(!$article->getId()){

                // on appelle seulement le seter de la date pour instancier un nouvel objet DateTime et créer une nouvelle date
                $article->setCreatedAt(new \DateTime);
            }

            $manager->persist($article);

            $manager->flush();

            // Quand le formulaire est soumis,
            // redirectToRoute() nous redirige vers la page ccontenant le détail de l'article qui vient d'être créé grâce à son id
            return $this->redirectToRoute('blog_show', [
                'id'=> $article->getId()
            ]);
        }


        // 2. On envoie le formulaire sur la vue/template
        return $this->render('blog/create.html.twig', [

            // createView() est une méthode de Symfony
            // qui contient un objet contenant un formulaire qui sera envoyé dans le template/ la vue. On gérera l'objet qui contient le formulaire avec TWIG.
            'formArticle'=> $form->createView(),
            // Ajout d'un indice 'editMode' qui pointe vers l'objet article. Le geter de l'entité Article retourne l'id de l'article
            // Si l'id est différent de null, c'est qu'il est connu en BDD et que l'id de l'article existe déjà.
            // Si id = null => insertion d'un nouvel article
            // Si id est !== null => modification de l'article existant 
            'editMode'=> $article->getid() !== null
            
        ]);
        
    }


    /**
     * // Route paramétrée qui attend l'id des articles de la BDD en paramètre
     * @Route("/blog/{id}", name="blog_show")
     */

        // Pour chaque template, on a une route et une méthode précises
        // show() est une méthode permettant de voir le détail d'un article

    public function show(ArticleRepository $repo, $id)

        /*
            show(ArticleRepository $repo) --> $repo est une variable de réception déclarée/instanciée directement en argument de la fonction show().
            Symfony a déjà fait '$repo = new ArticleRepository' pour nous. On peut la nommer à souhait. Elle réceptionne un objet issu de la classe ArticleRepository et sera appelée l146 pour appeler la méthode find()

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

        // Arguments render() = ('template_à_envoyer sur le navigateur', 'ARRAY options')
    }

    
}
