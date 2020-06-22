<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }


    /**
     * @Route ("/", name="home")
     */

    // argument 1 : point d'entrée du site, de l'application
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
}
