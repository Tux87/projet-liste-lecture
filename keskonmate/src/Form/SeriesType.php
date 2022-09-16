<?php

namespace App\Form;

use App\Entity\Actor;
use App\Entity\Genre;
use App\Entity\Series;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required'   => false,
                "label" => "Titre :",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('synopsis', TextareaType::class, [
                'required'   => false,
                "label" => "Synopsis",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => '3'
                ],
            ])
            ->add('releaseDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Sorti le :',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('image', TextType::class, [
                'required'   => false,
                "label" => "Affiche :",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('director', TextType::class, [
                'required'   => false,
                "label" => "Réalisateur :",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])                
            ->add('numberOfSeasons', TextType::class, [
                'required'   => false,
                "label" => "Nombre de saisons :",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]) 
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'label' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('actor', EntityType::class, [
                'class' => Actor::class,
                'label' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('homeOrder', IntegerType::class, [
                'required'   => false,
                "label" => "Ordre en en page d'accueil :",
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('createdAt', DateTimeType::class, [
                'input'  => 'datetime_immutable',
                'disabled' => 'disabled'
            ])
            ->add('updatedAt', DateTimeType::class, [
                'input'  => 'datetime_immutable',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Series::class,
            'allow_extra_fields' => true
        ]);
    }
}
