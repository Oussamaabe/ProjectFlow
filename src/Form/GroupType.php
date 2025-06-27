<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\Permission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du groupe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom du groupe ne peut pas être vide.',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Entrez un nom de groupe',
                    'class' => 'form-control'
                ],
                'help' => 'Ce nom sera utilisé pour identifier le groupe.',
            ])

            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => sprintf('%s (%s)', $user->getUsername(), $user->getUserIdentifier()),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Membres du groupe',
                'attr' => [
                    'class' => 'select2 form-control',
                    'data-placeholder' => 'Sélectionner les utilisateurs',
                    'data-allow-clear' => true,
                ],
                'by_reference' => false,
                'placeholder' => '',
            ])

            ->add('roles', ChoiceType::class, [
                'choices' => $this->getRoleChoices(),
                'multiple' => true,
                'expanded' => true,
                'label' => 'Rôles attribués',
                'choice_label' => fn($choice) => $choice,
                'attr' => [
                    'class' => 'role-checkboxes'
                ],
                'help' => 'Sélectionnez les rôles à assigner à ce groupe.',
            ])

            ->add('permissions', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Permissions assignées',
                'group_by' => fn(Permission $permission) => $permission->getCategory() ?? 'Général',
                'attr' => [
                    'class' => 'permission-checkboxes'
                ],
                'by_reference' => false,
                'choice_attr' => fn(Permission $permission) => [
                    'title' => $permission->getDescription() ?? $permission->getCode(),
                    'data-code' => $permission->getCode()
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
            'permission_categories' => [],
        ]);

        $resolver->setAllowedTypes('permission_categories', ['array']);
    }

    private function getRoleChoices(): array
    {
        return [
            'Administrateurs' => 'ROLE_ADMIN',
            'Agents Administratifs' => 'ROLE_ADMIN_AGENT',
            'Agents Techniques' => 'ROLE_TECH_AGENT',
        ];
    }
}
