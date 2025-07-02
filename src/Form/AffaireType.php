<?php

namespace App\Form;

use App\Entity\Affaire;
use App\Enum\AffaireStatus;
use App\Enum\AffaireType as AffaireTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'affaire',
                'attr' => [
                    'placeholder' => 'Entrez le titre de l\'affaire',
                    'class' => 'form-control'
                ],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'affaire',
                'choices' => [
                    'Bon de commande (BC)' => AffaireTypeEnum::BC->value,
                    'Appel d\'offres (AO)' => AffaireTypeEnum::AO->value,
                ],
                'placeholder' => 'Sélectionnez un type',
                'attr' => ['class' => 'form-select'],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En cours' => AffaireStatus::EN_COURS->value,
                    'Adjudicataire' => AffaireStatus::ADJUDICATAIRE->value,
                    'Perdu' => AffaireStatus::PERDU->value,
                    'Abandonné' => AffaireStatus::ABANDONNE->value,
                ],
                'placeholder' => 'Sélectionnez un statut',
                'attr' => ['class' => 'form-select'],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin (optionnel)',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('client', TextType::class, [
                'label' => 'Client',
                'attr' => [
                    'placeholder' => 'Nom du client',
                    'class' => 'form-control'
                ],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant (€)',
                'required' => false,
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => 'Montant de l\'affaire',
                    'class' => 'form-control'
                ],
                'row_attr' => ['class' => 'mb-3'],
                'html5' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Description de l\'affaire',
                    'class' => 'form-control'
                ],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('resultat', TextareaType::class, [
                'label' => 'Résultat détaillé',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Détails du résultat, analyse concurrentielle, leçons apprises...',
                    'class' => 'form-control'
                ],
                'row_attr' => ['class' => 'mb-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affaire::class,
            'attr' => ['id' => 'affaire-form'],
        ]);
    }
}