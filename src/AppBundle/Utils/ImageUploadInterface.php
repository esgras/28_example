<?php

namespace AppBundle\Utils;

interface ImageUploadInterface {

    public function getImageName();

    public function setImageFile($imageFile);

    public function getImage();

    public function setImage($image);

    public function getImageFile();
}