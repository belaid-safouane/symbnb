<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class, $this->getConfiguration("Prénom","Votre prénom .."))
            ->add('lastName',TextType::class, $this->getConfiguration("Nom","Votre Nom de famille .."))
            ->add('email',EmailType::class,$this->getConfiguration("Email","Votre adresse email"))
            ->add('picture',UrlType::class,$this->getConfiguration("photo de profile","URL de votre avatar .."))
            ->add('hash',PasswordType::class,$this->getConfiguration("Mot de passe","Choisissez un bon mdp !"))
            ->add('passwordConfirm', PasswordType::class,$this->getConfiguration("Confirmation de mot de passe", "veuillez confirmer votre mot de passe"))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction","Présentez vous en quelque mots .."))
            ->add('description',TextareaType::class,$this->getConfiguration("Description","C'est le moment de vous présenter en details !"))
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
