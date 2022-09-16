<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email :',
                'attr' => ['class' => 'form-control',
                'placeholder' => 'exemple@gmail.com']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class, 
                'required' => true,
                'mapped' => false,
                'first_options'  => ['label' => 'Mot de passe :',
                                     'attr' => ['class' => 'form-control',],],
                'second_options' => ['label' => 'Répéter le mot de passe :',
                                     'attr' => ['class' => 'form-control',],],
            ])
            ->add('userNickname', TextType::class, [
                'label' => 'Pseudo :',
                'attr' => ['class' => 'form-control',
                'placeholder' => 'jblebest']
            ])
            ->add('roles', ChoiceType::class, [
                "label" => false,
                'choices' => [
                    'SuperAdmin' => 'ROLE_SUPER_ADMIN',
                    'Admin' => 'ROLE_ADMIN',
                    'Catalogue Manager' => 'ROLE_CATALOGUE_MANAGER',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => false,
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
            'data_class' => User::class,
        ]);
    }
}
