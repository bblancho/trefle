<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Security;

class RecetteType extends AbstractType
{

    private $security ;

    public function __construct(Security $security )
    {
        $this->security = $security ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'required'  => true,
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
                'label'     => "Nom de la recette",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([ 'min' => 2,'max' => 50 ]),
                ]
            ])
            ->add('time', IntegerType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min'  => 1,
                    'max'  => 1440
                ],
                'label' => "Temps (en minutes)",
                'label_attr' => [
                    'class' => "form-label mt-4"
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(1441)
                ]
            ])
            ->add('nbPersonne', IntegerType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min'   => 1,
                    'max'   => 50,
                ],
                'label' => "Nombre de personnes",
                'label_attr'=>[
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(51),
                ]
            ])
            ->add('difficulte', IntegerType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min'   => 1,
                    'max'   => 5,
                ],
                'label' => "Difficulté de la recette ( de 1 à 5)",
                'label_attr'=>[
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(6),
                ]
            ])
            ->add('description', TextareaType::class,[
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'cols'  => 15,
                    'rows'  => 10, 
                ],
                'label' => " Description de la recette ",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('isFavori', CheckboxType::class, [
                'required' => false,
                'attr'=>[
                    'class' => "form-check-input"
                ],
                'label' => " Favori ?",
                'label_attr' => [
                    'class' => "form-check-label",
                ],
                'constraints' => [
                    new Assert\NotNull(),
                ]
            ])
            ->add('isPublique', CheckboxType::class, [
                'required' => true,
                'attr'=>[
                    'class' => "form-check-input"
                ],
                'label' => " Publique ?",
                'label_attr' => [
                    'class' => "form-check-label",
                ],
                'constraints' => [
                    new Assert\NotNull(),
                ]
            ])
            ->add('prix', MoneyType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => "Prix en ", 
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(1001),
                ]
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class, // looks for choices from this entity
                'query_builder' => function(IngredientRepository $repository)  {
                    return $repository->createQueryBuilder('i')
                        ->where('i.user = :user')
                        ->setParameter('user', $this->security->getUser() )
                    ;
                },
                'choice_value' => 'nom', // uses the Ingredient.nom property as the visible option string
                'multiple' => true, // used to render a select box, check boxes or radios
                'expanded' => false,
                'required' => false,
                'attr'=>[
                    'class' => "form-control "
                ],
                'label' => "Les ingrédients ",
                'label_attr' => [
                    'class' => "form-label mt-4",
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }


}
