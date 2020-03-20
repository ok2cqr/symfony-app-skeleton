<?php
declare(strict_types = 1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LostPasswordFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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

        $builder->add('lostPassword', SubmitType::class, [
            'label' => 'lostPassword.form.button.reset',
        ]);
    }
}
