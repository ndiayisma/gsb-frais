<?php

namespace App\Form;

use App\Entity\FicheFrais;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\LigneFraisForfait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneFraisHorsForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            /*->add('date', null, [
                'widget' => 'single_text',
            ])*/
            ->add('montant')
            /*->add('ficheFrais', EntityType::class, [
                'class' => FicheFrais::class,
                'choice_label' => 'id',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneFraisHorsForfait::class,
        ]);
    }
}
