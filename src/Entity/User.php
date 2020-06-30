<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

// L'objet UniqueEntity permet de préciser les champs qui attendent des données uniques. Ici, le champ "email". Penser à importer la classe.
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *  fields = {"email"},
 *  message = "Cette adresse email est déjà associée à un compte."
 * )
 */

// Pour pouvoir encoder le mot de passe, il faut que notre entité User implemente l'interface UserInterface.
class User implements UserInterface              
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    //  On appelle l'objet Assert et on pointe sur la contrainte Email pour transformer le champ en type ="email"
    // fait en sorte l'email que soit unique dans la BDD pour éviter les doublons.
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *  message="Cette adresse email '{{ value }}' n'est pas valide."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    // On importe l'objet Assert (cf l.7) et on pioche les méthodes Length et EqualTo.
    // Length est une contrainte de longueur 
    // EqualTo est une contrainte qui requiert que le champ password soit égal au champ confirm_password

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit contenir 8 caractères minimum.")
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les mots de passe ne correspondent pas.")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passe ne correspondent pas.")
     * 
     */

    // On ajoute une propriété public qui sera en charge de comparer le mot de posse
    // au mot de passe renseigné dans le formulaire.
    // Inutile d'ajouter une annotation ORM, ni d'ajouter des seters et geters
    // car ils ne seront pas ajoutés en BDD.
    public $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /*
        Pour pouvoir encoder le mot de passe, il faut que notre entité User implemente l'interface UserInterface.
        Cette interface contient des méthodes que nous sommes obligés de déclarer :
        getPassword(), getUsername(), getRoles(), getSalt() et eraseCredentials().    
    */

    // Cette méthode est uniquement destinée à nettoyer les mots de passe en texte brut éventuellement stockés
    public function eraseCredentials()
    {

    }

    // Cette méthode renvoie la chaîne de caractères non encodée que l'utilisateur a saisi et qui, à l'origine,
    // a été utilisée pour encoder le mot de passe. 
    public function getSalt()
    {

    }

    // Cette méthode renvoie un tableau de chaînes de caractères où sont stockés les rôles(droits) accordés à l'utilisateur.
    // (administrateur ou utilisateur classique)
    public function getRoles()
    {
        return ['ROLE_USER'];
    }


}

