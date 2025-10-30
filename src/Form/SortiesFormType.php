<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Etat;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SortiesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de la sortie'])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la sortie',
            ])
            ->add('duree', IntegerType::class, ['label' => 'Durée (minutes)'])
            ->add('dateCloture', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de clôture des inscriptions',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom_lieu',
                'placeholder' => 'Choisir un lieu',
                'label' => 'Lieu',
            ])
            ->add('nbInscriptionMax', IntegerType::class, ['label' => 'Nombre maximum d’inscriptions'])
            ->add('descriptionInfos', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
            ])
            ->add('urlPhoto',FileType::class,[
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/bmp'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image avec un format valide',
                    ])
                ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
