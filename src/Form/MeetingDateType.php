<?php

namespace App\Form;

use App\Entity\MeetingDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_at',
            DateTimeType::class,
            [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'js-datetimepicker'
                ]
            ]
        )
            ->add('end_at', DateTimeType::class, [
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MeetingDate::class,
            'attr' => ['id' => 'add-date'],
            'format' => 'yyyy-MM-dd  HH:i'
        ]);
    }
}
