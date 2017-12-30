<?php

namespace Infrastructure\Forms;

use Domain\Model\Jugador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class JugadorUpdateAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dni', TextType::class)
            ->add('nombre', TextType::class)
            ->add('telefono', TextType::class, ['constraints' => new Assert\Regex('@\d+@') ])
            ->add('email', EmailType::class, ['constraints' => new Assert\Email() ])
            ->add('password',  RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas deben de coincidir',
                'options' => [
                    'attr' => array('class' => 'password-field'),
                    'always_empty' => false,
                ],
                'required' => false,
                'first_options'  => array('label' => 'Contraseña'),
                'second_options' => array('label' => 'Repita Contraseña'),
            ))
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Inactivo' => 'ROLE_NONE',
                    'Usuario' => 'ROLE_USER',
                    'Administrador' => 'ROLE_ADMIN',
                ]
            ])
            ->add('cancel', ResetType::class, array('label' => 'Cancelar'))
            ->add('submit', SubmitType::class, array('label' => 'Guardar'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Jugador::class,
        ));
    }
}