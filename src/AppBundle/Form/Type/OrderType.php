<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\EmailNotUsed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
//                    new EmailNotUsed()
                ],
            ])
//            ->add('password', PasswordType::class)
            ->add('accept', CheckboxType::class, array(
                'required' => true,
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'You must agree to terms'])
                ],
                'invalid_message' => 'This field is required',
                'data' => false
            ))
            ->add('sendCheck', CheckboxType::class, array(
                'required' => false,
                'label' => false,
//                'mapped' => false,
                'data' => false
            ))
            ->add('submit', SubmitType::class, ['label' => 'Buy']);
    }
//
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

}