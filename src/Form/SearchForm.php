<?php

namespace App\Form;

use App\Entity\Type;
use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q',TextType::class,[
                'label'=>false,
                'required'=>false,
                'attr' => ['placeholder' => 'Rechercher '],
                ])
            
            ->add('type',EntityType::class,[
                'label'=>false,
                'required'=>false,
                'class'=> Type::class,
                'expanded'=>true,
                'multiple'=>true

            ])
            ->add('min',NumberType::class,[
                'label'=>false,
                'required'=>false,
                'attr'=> ['placeholder' => 'Prix Min '],
            ])
            ->add('max',NumberType::class,[
                'label'=>false,
                'required'=>false,
                'attr'=> ['placeholder' => 'Prix Max '],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class'=>SearchData::class,
           'method'=>'GET',
           'csrf_po=rotection'=>false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
