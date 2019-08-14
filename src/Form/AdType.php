<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("titre", "Tapez un super titre pour votre annonce"))
            ->add('coverImage',UrlType::class, $this->getConfiguration("URL de l'image","adresse image",['required'=>false]))
            ->add('introduction',TextType::class, $this->getConfiguration("Introduction","Donner une description globale de l'annonce"))
            ->add('content',TextareaType::class, $this->getConfiguration("Description detaillé","tapez une description qui donne envie de venir chez vous"))
            ->add('price',MoneyType::class, $this->getConfiguration("Prix par nuit","Indiquez le prix que vous voulez pour une nuit "))
            ->add('rooms',IntegerType::class, $this->getConfiguration("Nombre de chambre", "le nombre chambre disponible"))
            ->add('images',CollectionType::class,['entry_type' => ImageType::class,'allow_add' => true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
            
        ]);
    }
}
