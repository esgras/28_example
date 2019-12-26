<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
#use Savvot\Random\XorShiftRand;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class StringHelper
{
    private $encoderFactory;
    private $random;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
        $this->random = new \chriskacerguis\Randomstring\Randomstring();
    }

    public function generateSlug()
    {
        return strtolower($this->random->generate(12));
    }

    public function getPasswordAndHash(User $user)
    {
        $password = $this->random->generate(10);
        if (!preg_match('/\d/', $password)) {
            $password .= mt_rand(0, 9);
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        $hash = $encoder->encodePassword($password, $user->getSalt());

        return [
            'password' => $password,
            'hash' => $hash
        ];
    }

    public function encodePassword(User $user, $password)
    {
        return $this->encoderFactory->getEncoder($user)
            ->encodePassword($password, $user->getSalt());
    }

    public function generateHash()
    {
        return strtolower($this->random->generate(32));
    }

    public function makeHiddenPassword($password, $visibleLength=2)
    {
        return str_repeat('*', strlen($password) - $visibleLength) .  substr($password, - $visibleLength);
    }


    public function makeRandomCode($len)
    {
        $first = array_merge(range(65, 90), range(97, 122), range(48, 57));
        $str = join('', array_map(function($el) {
            return chr($el);
        }, $first));

        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= $str[mt_rand(0, strlen($str) - 1)];
        }

        return $result;
    }


    public function ucfirstAll($string)
    {
        $arr = explode(' ', $string);
        $arr = array_map([$this, 'ucfirst'], $arr);

        return join(' ', $arr);
    }

    public function ucfirst($str)
    {
        return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
    }

}