<?php
// src/Form/ProjetManagementType.php

namespace App\Form;

use App\Entity\Projet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\CallbackTransformer;

class ProjetManagementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deadlines', CollectionType::class, [
                'entry_type' => DeadlineType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => false,
                'attr' => ['class' => 'collection-deadlines']
            ])
            ->add('priceList', CollectionType::class, [
                'entry_type' => PriceListItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => false,
                'attr' => ['class' => 'collection-prices']
            ])
            ->add('invoices', CollectionType::class, [
                'entry_type' => InvoiceType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => false,
                'attr' => ['class' => 'collection-invoices']
            ])
            ->add('documents', CollectionType::class, [
                'entry_type' => DocumentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => false,
                'attr' => ['class' => 'collection-documents']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer les modifications',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);

        // Transformateur pour gérer les données de collection
        $arrayTransformer = new CallbackTransformer(
            function ($data) {
                return $data;
            },
            function ($data) {
                return is_array($data) ? $data : [];
            }
        );

        $builder->get('deadlines')->addModelTransformer($arrayTransformer);
        $builder->get('priceList')->addModelTransformer($arrayTransformer);
        $builder->get('invoices')->addModelTransformer($arrayTransformer);
        $builder->get('documents')->addModelTransformer($arrayTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
            'validation_groups' => ['Default', 'management']
        ]);
    }
}