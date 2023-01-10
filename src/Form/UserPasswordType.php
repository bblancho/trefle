<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            ->add('oldPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 180,
                ],
                'mapped' => false ,
                'label' => 'Ancien mot de passe',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([ 'min' => 2,'max' => 180 ]),
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'options' => [
                    'attr' => [
                        'class' => 'form-control password-field',
                    ],
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                ],
                'first_options'  => ['label' => 'Mot de passe',],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([ 'min' => 2,'max' => 50 ]),
                ],
                'invalid_message' => 'Les mots de passes ne correspondent pas.',
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
