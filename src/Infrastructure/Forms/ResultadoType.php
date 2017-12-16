<?php

namespace Infrastructure\Forms;

use Domain\Model\Reserva;
use Domain\Model\Resultado;
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
class ResultadoType extends AbstractType
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
                    '10:00 a 12:00' => 1,
                    '12:00 a 14:00' => 2,
                    '14:00 a 16:00' => 3,
                    '16:00 a 18:00' => 4,
                    '18:00 a 20:00' => 5,
                    '20:00 a 22:00' => 6,
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
            'data_class' => Resultado::class,
        ));
    }
}
