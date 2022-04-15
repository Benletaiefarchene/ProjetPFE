<?php

namespace App\Form;

use App\Entity\OffreFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('lieu_formation')
            ->add('DateDebut',DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('dureeformation')
            ->add('price')
            ->add('description',TextareaType::class)
            
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreFormation::class,
        ]);
    }
}
