<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Etat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('noSortie')
            ->add('nom')
            ->add('datedebut')
            ->add('duree')
            ->add('datecloture')
            ->add('nbinscriptionmax')
            ->add('descriptioninfos')
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'libelle', // ou le nom du champ à afficher
                'placeholder' => 'Choisir un état',
            ])
            ->add('urlPhoto');

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
