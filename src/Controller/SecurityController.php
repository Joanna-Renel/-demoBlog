<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{   
    // Cette méthode retourne le template registration.html.twig qui permettra de s'inscrire et de créer un compte.
    /**
     * @Route("/inscription", name="security_registration")
     */

    // Penser à importer les classes Request et EntityManagerInterface
    // Request est une classe prédéfinie en Symfony qui stocke les données véhiculées par les superglobales
    // EntityManagerInterface est une classe prédéfinie en Symfony qui permet de manipuler les lignes de la BDD (INSERT, UPDATE, DELETE)
    // UserPasswordEncoderInterface est une classe prédéfinie en Symfony qui contient des méthodes abstraitres pour encoder le mot de passe dans la BDD
    // Il faut donc les déclarer dans le controller, même si elles ne seront pas utilisées par la suite
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {   
        // Pour insérer un nouvel utilisateur, on passer par l'entité User.
        $user = new User;

        // On importe la class RegistrationType qui contient le formulaire de création de compte
        // createForm() permet d'importer et de générer le formulaire à partir de la class RegistrationType
        // Ce formulaire sera destiné à remplir l'objet $user
        $form = $this->createForm(RegistrationType::class, $user);

        dump($request);

        /* Résultat du dump($request)

        +request: Symfony\Component\HttpFoundation\InputBag {#91 ▼
        #parameters: array:1 [▼
        "registration" => array:5 [▼
        "username" => "Joanna"
        "email" => "test@gmail.com"
        "password" => "joanna"
        "confirm_password" => "joanna"
        "_token" => "kjJ8xdBTisocvEObmk4PY_08-Cuo5Z14aVCSWm3N1-g"
        ]
        */

        
        // récupère les données saisies du formulaire et les envoie remplir les seters de l'objet $user
        $form->handleRequest($request);
        
        // Si le formulaire a bien été validé et que les seters de $user sont correctement remplis, alors on entre dans le if.
        if($form->isSubmitted() && $form->isValid())
        {   
            // On transmet à la méthode encodePassword() de l'interface UserPasswordEncoderInterface le mot de passe du formulaire à encoder
            // $hash contient le mot de passe encodé
            $hash = $encoder->encodePassword($user, $user->getPassword());

            // On transmet le MDP au seter de l'objet $user
            $user->setPassword($hash);

            $manager->persist($user); // On prépare l'insertion
            $manager->flush(); // On exécute la requête d'insertion

            // addflash() est une méthode prédéfinie et qui provient du Security_Controller
            // Elle permet d'afficher des messages utilisateur

            $this->addFlash('success', 'Félicitations pour votre inscription. Vous pouvez maintenant vous connecter.');

            // Après l'inscription, l'utilisateur est redirigé vers la page de connexion
            return $this->redirectToRoute("security_login");
        }
        
        // 2e argument : envoyer le formulaire dans le template responsable de l'affichage
        // On crée l'indice 'form' à qui on envoie le formulaire avec createView() qui permet de créer un objet et d'envoyer le formulaire en TWIG
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     *
     */

    public function logout()
    {
        // Cette méthode ne retourne rien. Il nous suffit d'avoir une route pour la déconnexion.
    }
}
