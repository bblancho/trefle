<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([ 'min' => 2,'max' => 50 ]),
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([ 'min' => 2,'max' => 50 ]),
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
                'label' => 'Pseudo (facultatif)',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false,
                'constraints' => [
                    new Assert\Length([ 'min' => 2,'max' => 50 ]),
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
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
                'invalid_message' => 'Les mots de passesne correspondent pas.',
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
