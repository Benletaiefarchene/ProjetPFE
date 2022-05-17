<?php

namespace App\Form;

use App\Entity\CV;
use App\Entity\Competance;
use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
class CVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('sexe')
            ->add('pays')
            ->add('date_naissance',DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('ville')
            ->add('langue_preferee')
            ->add('photo',FileType::class , array('data_class'=>null))
            ->add('video',FileType::class , array('data_class'=>null))
            ->add('competances',CollectionType::class,[
                'entry_type' => CompetanceType::class,
                'entry_options' => [
                    'label' => false
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ])
            ->add('experiences',CollectionType::class,[
                'entry_type' => ExperienceType::class,
                'entry_options' => [
                    'label' => false
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ])
           
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CV::class,
        ]);
    }
}
