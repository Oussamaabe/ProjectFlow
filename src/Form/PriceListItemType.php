<?php
// src/Form/PriceListItemType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PriceListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control mb-2']
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'attr' => ['class' => 'form-control mb-2']
            ])
            ->add('unit', TextType::class, [
                'label' => 'Unité',
                'attr' => ['class' => 'form-control mb-2']
            ])
            ->add('unitPrice', NumberType::class, [
                'label' => 'Prix unitaire',
                'attr' => ['class' => 'form-control mb-2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}