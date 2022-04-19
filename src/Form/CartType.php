<?php

namespace App\Form;

use App\Entity\Cart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity',IntegerType::class,[
                "label" => "Quantité ",
                "attr" =>[
                    "min" => 0,
                    "max" => $options["max"],
                    "value" => $options["quantity"]
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'max' => 1,
            'quantity' => 1
        ]);
    }
}
