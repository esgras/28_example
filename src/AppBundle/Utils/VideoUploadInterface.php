<?php

namespace AppBundle\Utils;

interface VideoUploadInterface {

    public function getVideoName();

    public function setVideoFile($videoFile);

    public function getVideo();

    public function setVideo($video);

    public function getVideoFile();
}