<?php

namespace AppBundle\Traits;

trait ForceUpdateTrait
{
    public function updateMark()
    {
        $this->setFire(1);
    }

    public function clearUpdateMark()
    {
        $this->setFire(0);
    }
}