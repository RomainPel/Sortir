<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreSortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSortie', TextType::class, ['label' => 'Le nom de la sortie contient'])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom_site',
                'placeholder' => 'Choisir un site',
                'label' => 'Site',
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Choisir un état',
                'label' => 'Etat',
            ])
            ->add('estOrganiqateur', CheckboxType::class, ['label' => 'Sorties dont je suis l\'organisateur/trice'])
            ->add('estInscrit', CheckboxType::class, ['label' => 'Sorties auxquelles je suis inscrit/e'])
            ->add('estPasInscrit', CheckboxType::class, ['label' => 'Sorties auxquelles je ne suis inscrit/e'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
