<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Subscriber;
use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\EmailNotUsed;
use AppBundle\Validator\Constraints\InviteEmailNotUsed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class InviteFriendType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [new Email(), new EmailNotUsed(),
//                    new InviteEmailNotUsed()
                ]
            ]);
    }
//
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

}