<?php
// src/Form/LigneCommandeAchatType.php
namespace App\Form;

use App\Entity\LigneCommandeAchat;
use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneCommandeAchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'designation',
                'placeholder' => 'Sélectionnez un article',
                'label' => 'Article',
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
            ])
            ->add('prixUnitaire', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix unitaire',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LigneCommandeAchat::class,
        ]);
    }
}
