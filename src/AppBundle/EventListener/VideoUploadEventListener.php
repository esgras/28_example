<?php

namespace AppBundle\EventListener;

//use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Service\FileUploader;
use AppBundle\Service\ImageHelper;
use AppBundle\Service\VideoHelper;
use AppBundle\Utils\ImageInterface;
use AppBundle\Utils\VideoUploadInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Utils\ImageUploadInterface;

class VideoUploadEventListener
{
    private $uploader;
    private $filename;
    private $tempDir;
    private $oldEntity;
    private $videoHelper;
    private $deletedId;


    public function __construct(FileUploader $uploader, VideoHelper $videoHelper, $tempDir)
    {
        $this->uploader = $uploader;
        $this->uploader->setTargetDir($tempDir);
        $this->tempDir = $tempDir;
        $this->videoHelper = $videoHelper;
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

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$this->checkInstanceOf($entity)) {
            return;
        }

        if (
            $entity->getVideoFile() !== $this->oldEntity->getVideoFile()
            && is_file($this->tempDir . $entity->getVideoName())

        ) {
            $this->videoHelper->makeVideo($this->tempDir, $entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->checkInstanceOf($entity)) {
            return;
        }

        if ($entity->getVideoFile() !== $this->oldEntity->getVideoFile()
            && is_file($this->tempDir . $entity->getVideoName())
        ) {
            $this->videoHelper->removeVideo($entity, $this->deletedId);
            $this->videoHelper->makeVideo($this->tempDir, $entity);
        }
    }

    protected function uploadFile($entity)
    {
        if (!$this->checkInstanceOf($entity)) {
            return;
        }
        /** @var VideoUploadInterface $entity */
        $video = $entity->getVideo();

        if ($video instanceof UploadedFile) {
            $this->filename = $this->uploader->upload($video, 'mp4');
            $entity->setVideoFile($this->filename);
        } elseif (is_file($video)) {
            $this->filename = $this->uploader->upload(new File($video));
            $entity->setVideoFile($this->filename);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->checkInstanceOf($entity)) {
            return;
        }

        $this->filename =$entity->getVideo();
        $entity->setImage(new File($this->uploader->getTargetDir() . $this->filename, false));
    }

    protected function checkInstanceOf($object)
    {
        return $object instanceof VideoUploadInterface;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->deletedId = $entity->getId();
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->checkInstanceOf($entity)) {
            $this->videoHelper->removeVideo($entity, $this->deletedId);
        }
    }
}