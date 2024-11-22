<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoisFicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fiches', ChoiceType::class, [
                'choices' => $options['data'],
                'choice_label' => function(FicheFrais $ficheFrais) {
                    return $ficheFrais->getMois()->format('Y-M'); // Format the month as "Year Month"
                },
                'placeholder' => 'Choisir un mois',
                'label' => 'Mois',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data' => [], // Allow passing months as choices
        ]);
    }
}
