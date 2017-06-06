<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

use Symfony\Component\Validator\Constraints\Length;

class VisitorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('lastname',		TextType::class, array(
				'label'			=>	'Nom',
				'constraints'	=>	new Length(
					array(
						'min' 			=> 2,
						'max' 			=> 255,
						'minMessage' 	=> 'Minimum de 2 caractères',
						'maxMessage' 	=> 'Maximum de 255 caractères'
					)),
				)
			)
			->add('firstname',		TextType::class, array(
				'label'			=>	'Prénom',
				'constraints'	=>	new Length(
					array(
						'min' 			=> 2,
						'max' 			=> 255,
						'minMessage' 	=> 'Minimum de 2 caractères',
						'maxMessage' 	=> 'Maximum de 255 caractères'
					))
				)
			)
			->add('country',		CountryType::class, array(
				'label'			=>	'Pays',
				'data'			=>	'FR'
			))
			->add('birthdate',		BirthdayType::class, array(
				'label'			=>	'Date de naissance',
				'format' 	=>	'dd-MM-yyyy',
			))
			->add('tariff',		CheckboxType::class, array(
				'label'			=>	'Tarif (il vous sera demandé de présenter à
				 l\'accueil votre carte d’étudiant, militaire, ou équivalent)',
				'required'		=>	false,
			))
		;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Visitor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_visitor';
    }
}
