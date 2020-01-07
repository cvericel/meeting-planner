<?php

namespace App\Form;

use App\Entity\Meeting;
use App\Entity\MeetingDate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            //->add('meeting_date', EntityType::class, [
            //    'class' => MeetingDate::class,
            //    'choice_label' =>
            //])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Meeting::class,
            'translation_domain' => 'forms'
        ]);
    }
}
