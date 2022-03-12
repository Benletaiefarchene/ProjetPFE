<?php

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre',TextType::class,['attr' => ['placeholder' => 'Titre '],'label' => false])
            ->add('Lieu',TextType::class,['attr' => ['placeholder' => 'Lieu '],'label' => false])
            ->add('Date_debut',DateType::class, [
                'widget' => 'single_text'

            ])
            ->add('Date_fin',DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('diplome',TextType::class,['attr' => ['placeholder' => 'Diplome '],'label' => false])
            ->add('Description',TextareaType::class,['attr' => ['placeholder' => 'Description '],'label' => false])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}
