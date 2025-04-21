<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoisFicheComptableType extends AbstractType
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // Cette condition permet au user qui ont ROLE_COMPTABLE de choisir un visiteur
        if ($this->security->isGranted('ROLE_COMPTABLE')) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($choice) {
                    return $choice->getNom() . ' ' . $choice->getPrenom();
                },
                'placeholder' => 'Choisir un visiteur',
                'label' => 'Visiteur',
                'required' => true,
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5',
                    'onchange' => 'this.form.submit();', // Soumet automatiquement le formulaire
                ],
            ]);
        }

        // Champ pour sélectionner un mois (affiché uniquement si des fiches sont disponibles)
        if (!empty($options['fiches'])) {
            $builder->add('fiches', ChoiceType::class, [
                'choices' => $options['fiches'],
                'choice_label' => function (FicheFrais $ficheFrais) {
                    return $ficheFrais->getMois()->format('Y-M');
                },
                'choice_value' => 'id', // Ensure the ID is used for the value
                'placeholder' => 'Choisir un mois',
                'label' => 'Mois',
                'required' => false,
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5',
                ],
            ]);
        }

        $builder->add('submit', SubmitType::class, [
            'attr' => ['class' => 'bg-blue-500 text-white px-4 py-2 rounded mt-4'],
            'label' => 'Valider',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'fiches' => [], // Par défaut, aucune fiche n'est disponible
        ]);
    }
}
