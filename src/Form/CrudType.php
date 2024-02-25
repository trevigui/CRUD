<?php

namespace App\Form;

use App\Entity\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrudType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content', null, [ // Ne pas oublier toutes les virgules.
                'required' => false, // Pour rendre le champ facultatif (car symfony les rend obligatoires par défaut. Attnetion, si le champ est not_null dans la base SQL, autoriser les champs null dans Syfony lancera une erreur HTTP.
            ]);
        // Les champs de form se rajoutent automatiquement ici après le make:form initial (et subséquents)
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
