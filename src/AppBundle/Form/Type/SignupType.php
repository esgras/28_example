<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\City;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class SignupType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
//            ->add('email', EmailType::class)
//            ->add('password', PasswordType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => true,
                'constraints' => [new NotBlank()],
                'first_options' => array('label' => 'Пароль', 'attr' => ['placeholder' => 'Пароль']),
                'second_options' => array('label' => 'Повторите пароль',  'attr' => ['placeholder' => 'Повторите пароль']),
                'invalid_message' => 'Password not matching'
            ))
            ->add('name', TextType::class, [
                'label' => 'Как Вас зовут',
                'constraints' => [new NotBlank()],
            ])
            ->add('city', ChoiceType::class, [
//                'choices' => $this->getCities(),
                'choices' => array_combine($options['cities'], $options['cities']),
                'multiple'=>false,
                'label' => 'Из какого Вы города',
                'placeholder' => false,
                'constraints' => [new NotBlank()],
            ])
//            ->add('city', EntityType::class, [
//                'class' => City::class,
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('c')
//                        ->orderBy('c.name', 'ASC');
//                },
//                'multiple'=>false,
//                'label' => 'Из какого Вы города',
//                'placeholder' => false,
//                'constraints' => [new NotBlank()],
//            ])
            ->add('age', IntegerType::class, [
                'label' => 'Сколько Вам лет',
                'constraints' => [new NotBlank(), new Range(['min' => 1])],
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => ['Мужчина' => User::MALE, 'Женщина' => User::FEMALE],
                'multiple'=>false,
                'label' => 'Ваш пол',
                'placeholder' => false,
                'constraints' => [new NotBlank()],
            ])
            ->add('scope', ChoiceType::class, [
                'label' => 'Сфера',
                'placeholder' => false,
                'choices' => $this->getScopes(),
                'required' => false
            ])
            ->add('position', TextType::class, [
                'label' => 'Должность',
                'required' => false
            ])
            ->add('aim', TextareaType::class, [
                'label' => 'Какова ваша цель отказа от алкоголя',
                'required' => false
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Начать курс'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('cities');
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false
        ]);
    }

    protected function getCities()
    {
        $cities = $this->loadCities();
        return array_combine($cities, $cities);
    }

    protected function loadCities()
    {
        return ['Москва', 'Смоленск'];
    }

    protected function getScopes()
    {
        $scopes = [null, 'IT, компьютеры, интернет',
            'Администрация, руководство среднего звена',
            'Бухгалтерия, аудит',
            'Гостинично-ресторанный бизнес, туризм',
            'Дизайн, творчество',
            'Красота, фитнес, спорт',
            'Культура, музыка, шоу-бизнес',
            'Логистика, склад, ВЭД',
            'Маркетинг, реклама, PR',
            'Медицина, фармацевтика',
            'Образование, наука',
            'Охрана, безопасность',
            'Продажи, закупки',
            'Рабочие специальности, производство',
            'Розничная торговля',
            'Секретариат, делопроизводство, АХО',
            'Сельское хозяйство, агробизнес',
            'СМИ, издательство, полиграфия',
            'Страхование',
            'Строительство, архитектура',
            'Сфера обслуживания',
            'Телекоммуникации и связь',
            'Топ-менеджмент, руководство высшего звена',
            'Транспорт, автобизнес',
            'Управление персоналом, HR',
            'Финансы, банк',
            'Юриспруденция'];

        return array_combine($scopes, $scopes);
    }

}