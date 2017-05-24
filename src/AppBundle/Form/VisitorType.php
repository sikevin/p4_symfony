<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class VisitorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('lastname',		TextType::class)
			->add('firstname',		TextType::class)
			->add('country',		TextType::class)
			->add('birthdate',		BirthdayType::class, [
				'format' => 'dd-MM-yyyy',
			])
			->add('tariff',		CheckboxType::class, [
				'label'	=>	'Tarif réduit (il vous sera demandé de présenter votre carte d’étudiant, militaire, ou équivalent)'
			])
			->add('next',		SubmitType::class, array(
				'attr' => array('class' => 'save'),
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
