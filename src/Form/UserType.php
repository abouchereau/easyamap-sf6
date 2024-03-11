<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{    

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,    array('label' => 'E-mail *'        ,'required' => true))
            ->add('firstname',TextType::class,  array('label' => 'Prénom'        ,'required' => false))
            ->add('lastname',TextType::class,   array('label' => 'Nom *'         ,'required' => true));
        if (!$options['is_new']) {
            $builder->add('username',TextType::class, array('label' => 'Identifiant de connexion' ,'required' => true));
        }
        else {
            $builder->add('username',HiddenType::class, array('required' => true));
        }

        if ($options['from_admin']) {
            $builder->add('password',PasswordType::class,   array('label' => 'Mot de passe *','required' => true));
            $builder->add('isAdherent', CheckboxType::class, array('label' => 'Adhérent', 'required' => false, 'attr' => array('checked' => 'checked')));
            $builder->add('isAdmin', CheckboxType::class, array('label' => 'Administrateur', 'required' => false));
        }
        if ($options['with_address']) {
            $builder->add('tel1',TextType::class, array('label'=>'Tel. 1', 'required' => false));
            $builder->add('tel2',TextType::class, array('label'=>'Tel. 2', 'required' => false));
            $builder->add('address',TextType::class, array('label'=>'Adresse', 'required' => false));
            $builder->add('zipcode',TextType::class, array('label'=>'Code Postal', 'required' => false));
            $builder->add('town',TextType::class, array('label'=>'Ville', 'required' => false));            
        }
        
        $builder->add('submit', SubmitType::class, ['label' => 'Mettre à jour','attr' => ['class' => 'pull-right btn-primary']]);
        $builder->add('cancel', ButtonType::class, ['label' => 'Annuler','attr' => ['onclick'=>'window.history.go(-1); return false;']]);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User'
        ));
    }
    
    public function configureOptions( OptionsResolver $resolver ) {
        $resolver->setDefaults( [
          'is_new' => null,
          'with_address' => null,
          'from_admin' => false
        ] );
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'amap_orderbundle_user';
    }
}
