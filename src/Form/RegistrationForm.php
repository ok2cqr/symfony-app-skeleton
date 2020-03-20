<?php
declare(strict_types = 1);

namespace App\Form;

use App\DataRequest\UserRegisterDataRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [
            'required' => true,
            'label' => 'login.form.label.email',
            'constraints' => [
                new NotBlank(),
                new Email(),
            ],
        ]);

        $builder->add('fullName', TextType::class, [
            'label' => 'login.form.label.full_name',
        ]);

        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => ['label' => 'login.form.label.password'],
            'second_options' => ['label' => 'login.form.label.verify_the_password'],
        ]);

        $builder->add('register', SubmitType::class, [
            'label' => 'register.form.button.register',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegisterDataRequest::class,
        ]);
    }
}
