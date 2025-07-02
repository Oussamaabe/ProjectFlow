<?php
// src/Form/SocieteType.php
namespace App\Form;

use App\Entity\Societe;
use App\Form\DocumentSocieteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocieteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raisonSociale')
            ->add('capital')
            ->add('rc')
            ->add('tp')
            ->add('ifFiscal')
            ->add('cnss')
            ->add('ice')
            ->add('banque')
            ->add('rib')
            ->add('siteWeb')
            ->add('telephone')
            ->add('fax')
            ->add('email')
            ->add('documents', CollectionType::class, [
                'entry_type' => DocumentSocieteType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Societe::class,
        ]);
    }
}
