<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                "label" => "Adresse email",
                "attr" => [
                    "placeholder" => "Adresse email"
                ]
            ])
            ->add('firstname',TextType::class,[
                "label" => "Nom",
                "required" => false,
                "attr" => [
                    "placeholder" => "Pas renseigné"
                ]
            ])
            ->add('lastname',TextType::class,[
                "label" => "Prénom",
                "required" => false,
                "attr" => [
                    "placeholder" => "Pas renseigné"
                ]
            ])
            ->add('phoneNumber',TelType::class,[
                "label" => "Numéro de téléphone",
                "required" => false,
                "attr" => [
                    "placeholder" => "Pas renseigné"
                ]
            ])
            ->add('adress',TextType::class,[
                "label" => "Adresse Postale",
                "required" => false,
                "attr" => [
                    "placeholder" => "Pas renseigné"
                ]
            ])
            ->add('city',TextType::class,[
                "label" => "Ville",
                "required" => false,
                "attr" => [
                    "placeholder" => "Pas renseigné"
                ]
            ])
            ->add('postalCode',NumberType::class,[
                "label" => "Code postal",
                "required" => false,
                "attr" => [
                    "placeholder" => "Pas renseigné"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
