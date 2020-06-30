<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Class qui permet de générer un formulaire
class ArticleType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            // On rajoute un champ qui permettra de sélectionner une catégorie à la création ou à la modification d'un article
            // EntityType::class précise de quel type de donnée il s'agit. Ici, category est de type "entité"
            ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'title' // Le label choisi est le titre de chaque catégorie
            ])
            ->add('content')
            ->add('image')
            // Champ inutile car dans notre cas, la date se génère automatiquement quand on crée l'article.
            //->add('createdAt')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
