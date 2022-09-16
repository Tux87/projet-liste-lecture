<?php

namespace App\Form;

use App\Entity\Series;
use App\Entity\User;
use App\Entity\UserList;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserlistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'label' => false,
                'attr'  => ['class' => "form-control"]
            ])
            ->add('series', EntityType::class, [
                'class' => Series::class,
                'label' => false,
                'multiple' => false,
                'attr'  => ['class' => "form-control"]
            ])
            ->add('seasonNb', TextType::class, [
                'label' => "Saison :",
                'attr'  => ['class' => "form-control"]
            ])
            ->add('episodeNb', TextType::class, [
                'label' => "Episode :",
                'attr'  => ['class' => "form-control"]
            ])
            ->add('type', TextType::class, [
                'label' => "Type :",
                'attr'  => ['class' => "form-control"]
            ])
            ->add('createdAt', DateTimeType::class, [
                'input'  => 'datetime_immutable',
                'disabled' => 'disabled',
            ])
            ->add('updatedAt', DateTimeType::class, [
                'input'  => 'datetime_immutable',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserList::class,
        ]);
    }
}
