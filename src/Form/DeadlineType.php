<?php
// src/Form/DeadlineType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;

class DeadlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'étape',
                'attr' => ['class' => 'form-control mb-2']
            ])
            ->add('planned', TextType::class, [
                'label' => 'Date prévue',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2 date-picker',
                    'placeholder' => 'YYYY-MM-DD'
                ]
            ])
            ->add('actual', TextType::class, [
                'label' => 'Date réelle',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2 date-picker',
                    'placeholder' => 'YYYY-MM-DD'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Planifié' => 'planned',
                    'En cours' => 'in_progress',
                    'Terminé' => 'completed',
                    'En retard' => 'delayed'
                ],
                'attr' => ['class' => 'form-select mb-2']
            ]);

        // Transformateurs pour les dates
        $dateTransformer = new CallbackTransformer(
            // Transforme DateTime en string pour l'affichage
            function ($date) {
                return $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date;
            },
            // Transforme string en DateTime pour le stockage
            function ($dateString) {
                return $dateString ? \DateTime::createFromFormat('Y-m-d', $dateString) : null;
            }
        );

        $builder->get('planned')->addModelTransformer($dateTransformer);
        $builder->get('actual')->addModelTransformer($dateTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}