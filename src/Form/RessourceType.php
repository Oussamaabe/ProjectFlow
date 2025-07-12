<?php
// src/Form/RessourceType.php
namespace App\Form;

use App\Entity\Ressource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\RessourceType as RessourceTypeEnum;
class RessourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de ressource',
                'choices' => [
                    'Ressource humaine' => RessourceTypeEnum::HUMAIN->value,
                    'Ressource matérielle' => RessourceTypeEnum::MATERIEL->value,
                    'Matière première' => RessourceTypeEnum::MATIERE->value,
                ],
                'attr' => [
                    'class' => 'form-control mb-2',
                    'onchange' => 'updateFormFields(this.value)'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control mb-2']
            ])
            ->add('role', TextType::class, [
                'label' => 'Rôle',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'id' => 'ressource_role'
                ]
            ])
            ->add('heures', NumberType::class, [
                'label' => 'Heures engagées',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'id' => 'ressource_heures'
                ]
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'id' => 'ressource_quantite'
                ]
            ])
            ->add('unite', TextType::class, [
                'label' => 'Unité',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'id' => 'ressource_unite'
                ]
            ])
            ->add('coutUnitaire', NumberType::class, [
                'label' => 'Coût unitaire (€)',
                'attr' => ['class' => 'form-control mb-2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressource::class,
        ]);
    }
}