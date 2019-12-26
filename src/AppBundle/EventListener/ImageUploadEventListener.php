<?php

namespace AppBundle\EventListener;

//use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Service\FileUploader;
use AppBundle\Service\ImageHelper;
use AppBundle\Utils\ImageInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Utils\ImageUploadInterface;
use Symfony\Component\VarDumper\VarDumper;

class ImageUploadEventListener
{
    private $uploader;
    private $filename;
    private $avatarDir;
    private $avatarThumbDir;
    private $tempDir;
    private $oldEntity;
    private $imageHelper;
    private $deletedId;


    public function __construct(FileUploader $uploader, ImageHelper $imageHelper, $tempDir)
    {
        $this->uploader = clone $uploader;
        $this->uploader->setTargetDir($tempDir);
        $this->tempDir = $tempDir;
        $this->imageHelper = $imageHelper;
        $this->avatarDir = '';
        $this->avatarThumbDir = '';
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->checkInstanceOf($entity)) {
            return;
        }
        $entity->clearUpdateMark();
        $this->oldEntity = clone $entity;
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$this->checkInstanceOf($entity)) {
            return;
        }
        
        $entity->clearUpdateMark();
        $this->oldEntity = clone $entity;
        $this->uploadFile($entity);
    }

    protected function uploadFile($entity)
    {
        if (!$this->checkInstanceOf($entity)) {
            return;
        }
        /** @var User $entity */

        $image = $entity->getImage();

        if ($image instanceof UploadedFile) {
            $this->filename = $this->uploader->upload($image);
            $entity->setImageFile($this->filename);
        } elseif (is_file($image)) {
            $this->filename = $this->uploader->upload(new File($image));
            $entity->setImageFile($this->filename);
        }

    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->checkInstanceOf($entity)) {
            return;
        }

//        if (!$entity instanceof Test) {
//            return;
//        }

        $this->filename =$entity->getImage();
        $entity->setImage(new File($this->uploader->getTargetDir() . $this->filename, false));
    }

    private function makeImageCollection($tempDir, $entity, $removeTempImage=false)
    {
        $tempDir = rtrim($tempDir, '/') . '/';
        $file = $tempDir . $entity->getImageName();

//        $path = $this->imageHelper->getImageDir($entity);
        copy($file,
            $this->imageHelper->getImageDir($entity) . $entity->getImageName());

        if ($removeTempImage) {
            unlink($file);
        }

        $this->imageHelper->makeCollection($entity);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->checkInstanceOf($entity)) {
            return;
        }

        if (
            $entity->getImageFile() !== $this->oldEntity->getImageFile()
            && is_file($this->tempDir . $entity->getImageName())

        ) {
            $this->makeImageCollection($this->tempDir, $entity, true);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->checkInstanceOf($entity)) {
            return;
        }


        if ($entity->getImageFile() !== $this->oldEntity->getImageFile()
            && is_file($this->tempDir . $entity->getImageName())
        ) {
            $this->imageHelper->removeCollection($this->oldEntity);
            $this->makeImageCollection($this->tempDir, $entity, true);
        }
    }

    protected function makeThumbnail($src, $dest, $size)
    {
        (new ImageHelper)->thumbnail($src, $dest, $size);
    }

    protected function checkInstanceOf($object)
    {
        return $object instanceof ImageUploadInterface;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->deletedId = $entity->getId();
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof  ImageInterface) {
            $this->imageHelper->removeCollection($entity,
            $this->imageHelper->makeImageDirectories($entity, $this->deletedId));
        }
    }
}