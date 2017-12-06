<?php

namespace Infrastructure\Forms;

use Domain\Model\Reserva;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pista', ChoiceType::class, [
                'choices' => [
                    'Pabellón Parderrubias' => Reserva::_PABELLON_,
                    'Pista Exterior Parderrubias' => Reserva::_EXTERIOR_,
                ]
            ])
            ->add('fecha', DateType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text'
            ])
            ->add('hora', ChoiceType::class, [
                'choices' => [
                    '- Escoje una hora -' => 0,
                    '10 a 12' => 1,
                    '12 a 14' => 2,
                    '14 a 16' => 3,
                    '16 a 18' => 4,
                    '18 a 20' => 5,
                    '20 a 22' => 6,
                ]
            ])
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Reservar'
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Reserva::class,
        ));
    }
}
