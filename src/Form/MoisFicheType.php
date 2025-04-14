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
                'choice_attr' => ['class' => 'text-gray-700'],
                'placeholder' => 'Choisir un mois',
                'label' => 'Mois',
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'bg-blue-500 text-white px-4 py-2 rounded mt-4'],
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
