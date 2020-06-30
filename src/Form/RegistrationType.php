<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('username')
            // PasswordType est une class prédéfinie en Symfony
            // qui permet de créer un champ de type password et de masquer le password
            ->add('password', PasswordType::class)
            // Ajoute un champ afin de confirmer le mdp.
            // Ce champ n'a pas été créé dans l'entité User car il ne sera pas inséré en BDD.
            ->add('confirm_password')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
