<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType {

    /**
     * Permet d'avoir la configuration de base d'un champ !
     *
     * @param String $label
     * @param String $placeholder
     * @param String $placeholder
     * @return array
     */
    protected function getConfiguration($label,$placeholder,$options=[]){
        //fusionner deux tableaux
        return array_merge([
            'label' =>$label,
            'attr' =>[
                'placeholder' => $placeholder
            ]
            ], $options);
    }


}