<?php

namespace App\Form;

use App\Entity\OffreFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description',TextareaType::class)
            ->add('folder',FileType::class , array('data_class'=>null))
            ->add('Role', ChoiceType::class, [
                'choices'  => [
                    'Recruteur' => 0,
                    'Candidat' => 1,
                ],
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'placeholder'=>false,
                
            ])
            ->add('Submit',SubmitType::class)           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreFormation::class,
        ]);
    }
}
