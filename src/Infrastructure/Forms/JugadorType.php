<?php

namespace Infrastructure\Forms;

use Domain\Model\Jugador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class JugadorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dni', TextType::class)
            ->add('nombre', TextType::class)
            ->add('telefono', TextType::class, ['constraints' => new Assert\Regex('@\d+@') ])
            ->add('email', EmailType::class, ['constraints' => new Assert\Email() ])
            ->add('password', PasswordType::class)
            ->add('foto', FileType::class, ['required' => false])
            ->add('submit', SubmitType::class, array('label' => 'Registrarse'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Jugador::class,
        ));
    }
}