<?php

namespace App\Form;

use App\Validator\PasswordSecurity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe ne correspondent pas.',
            'options' => ['attr' => ['class' => 'password-field']],
            'constraints' => [new PasswordSecurity('strict')],
            'required' => true,
            'first_options'  => ['label' => 'Nouveau mot de passe *'],
            'second_options' => ['label' => 'Confirmer nouveau mot de passe *'],
        ]);

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

        ] );
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'amap_orderbundle_password';
    }
}

