<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisieFicheFraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('km', IntegerType::class, [
                'label' => 'Frais Kilometrique',
                'label_attr' => ['class' => 'text-sm font-medium text-gray-500'],
                'row_attr' => ['class' => 'overflow-x-auto bg-white shadow-md rounded-lg relative z-50'],
                'required' => true,
            ])
            ->add('etape', IntegerType::class, [
                'label' => 'Forfait Etape',
                'label_attr' => ['class' => 'text-sm font-medium text-gray-500'],
                'row_attr' => ['class' => 'overflow-x-auto bg-white shadow-md rounded-lg relative z-50'],
                'required' => true,
            ])
            ->add('nuit', IntegerType::class, [
                'label' => 'Nuitée Hôtel',
                'label_attr' => ['class' => 'text-sm font-medium text-gray-500'],
                'row_attr' => ['class' => 'overflow-x-auto bg-white shadow-md rounded-lg relative z-50'],
                'required' => true,
            ])
            ->add('resto', IntegerType::class, [
                'label' => 'Repas Restaurant',
                'label_attr' => ['class' => 'text-sm font-medium text-gray-500'],
                'row_attr' => ['class' => 'overflow-x-auto bg-white shadow-md rounded-lg relative z-50'],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
