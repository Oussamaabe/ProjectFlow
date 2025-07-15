<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Fournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference')
            ->add('designation')
            ->add('prixUnitaire', NumberType::class, [
                'scale' => 2,
                'required' => true,
                'html5' => true,
                'attr' => ['step' => '0.01'],
            ])
            ->add('uniteMesure')
            ->add('description')
            ->add('categorie')
            ->add('tva')
            ->add('actif')
            ->add('dateCreation')
            ->add('dateModification')
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
