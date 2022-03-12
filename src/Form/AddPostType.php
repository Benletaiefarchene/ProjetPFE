<?php

namespace App\Form;

use App\Entity\OffreEmploi;
use App\Entity\Recruteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


use Symfony\Component\Form\Extension\Core\Type\FileType;

class AddPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description',TextareaType::class)
            ->add('date_Offre' ,DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('titre')
            ->add('type')
            ->add('categorie')
            ->add('salaire')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreEmploi::class,
        ]);
    }
}