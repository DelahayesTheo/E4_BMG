<?php

namespace BMG\gestBundle\Form;

use BMG\gestBundle\BMGgestBundle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OuvrageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class)
            ->add('genre', EntityType::class, array(
                'class' => 'BMGgestBundle:Genre',
                'choice_label' => 'libGenre',
                'multiple' => false,
                'expanded' => false
            ))
            ->add('salle',ChoiceType::class, array(
                'choices' => array(
                    1 => 1,
                    2 => 2
                ),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('rayon', TextType::class)
            ->add('dateAcquisition', DateType::class)
            ->add('save',SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BMG\gestBundle\Entity\Ouvrage'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bmg_gestbundle_ouvrage';
    }

}
