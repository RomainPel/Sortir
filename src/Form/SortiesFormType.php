<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Etat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de la sortie'])
            ->add('datedebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de début',
            ])
            ->add('duree', IntegerType::class, ['label' => 'Durée (minutes)'])
            ->add('datecloture', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de clôture',
            ])
            ->add('nbinscriptionmax', IntegerType::class, ['label' => 'Nombre maximum d’inscriptions'])
            ->add('descriptioninfos', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Choisir un état',
                'label' => 'État',
            ])
            ->add('urlPhoto', UrlType::class, [
                'required' => false,
                'label' => 'URL de l’image',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
