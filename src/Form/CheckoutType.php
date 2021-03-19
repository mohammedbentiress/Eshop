<?php

namespace App\Form;

use App\Entity\Shipping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city',TextType::class)
            ->add('postalCode',TextType::class)
            ->add('address',TextareaType::class)
            ->add('firstName',TextType::class)
            ->add('lastName',TextType::class)
            ->add('Email',EmailType::class)
            ->add('mobileNumber',TelType::class)
            ->add('country',CountryType::class)
            ->add('state',TextType::class)
            ->add('createAcc',CheckboxType::class, [
                'label'    => 'Create an account?',
                'required' => false,
                'mapped'=>false
            ])
            ->add('paymentMethode',ChoiceType::class,[
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'choices' => [
                    'Paypal' => 'paypal',
                    'Payoneer' => 'payoneer',
                    'Check Payment' => 'checkPayment',
                    'Direct Bank Transfer' => 'DBT',
                    'Cash on Delivery' => 'COD',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shipping::class,
        ]);
    }
}
