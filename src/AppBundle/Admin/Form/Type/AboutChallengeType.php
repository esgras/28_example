<?php

namespace AppBundle\Admin\Form\Type;

use AppBundle\Entity\CompanyPage;
use AppBundle\Entity\First;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class AboutChallengeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $page = $options['page'];

        $builder
            ->add('title', TextType::class, [
//            'data' => $page->getTitle()
        ])
//            ->add('image', FileType::class, [])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('page');

        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => CompanyPage::class
        ]);
    }
}