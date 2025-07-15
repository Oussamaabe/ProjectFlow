<?php
// src/Form/CommandeAchatType.php
namespace App\Form;

use App\Entity\CommandeAchat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeAchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', TextType::class, ['label' => 'Numéro de commande'])
            ->add('dateCommande', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de commande',
            ])
            ->add('statut', TextType::class, ['label' => 'Statut'])
            ->add('fournisseur', EntityType::class, [
                'class' => 'App\Entity\Fournisseur',
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un fournisseur',
                'label' => 'Fournisseur',
            ])
            ->add('lignes', CollectionType::class, [
                'entry_type' => LigneCommandeAchatType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Lignes de commande',
                'prototype' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CommandeAchat::class,
        ]);
    }
}
