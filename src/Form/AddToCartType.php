<?php

namespace App\Form;

use App\Entity\OrderLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AddToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('quantity',IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'd-inline'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderLine::class,
        ]);
    }
}
