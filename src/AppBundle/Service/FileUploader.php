<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;

    public function __construct(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(File $file, $ext='')
    {
        $ext = empty($ext) ? $file->guessExtension() : $ext;
        $fileName = md5(uniqid()) . '.' . $ext;
        $file->move($this->targetDir, $fileName);


        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }

    public function setTargetDir(string $targetDir)
    {
        $this->targetDir = $targetDir;

        return $this;
    }
}