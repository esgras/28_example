<?php

namespace AppBundle\Service;


use AppBundle\Entity\Day;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Utils\VideoInterface;
use Doctrine\Common\Util\ClassUtils;
use Imagine\Video\Box;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Imagine\Gd\Imagine;
use AppBundle\Entity\UserMedia;

class VideoHelper
{
    private $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }

    public function getLink($object, $type = 'origin')
    {
        $baseDirectoryPath = $this->getBaseDirectory($object);
    }

    public function getName($src, $type='')
    {
        $prefix = substr($src, 0, strrpos($src, '.'));
        $last = substr($src,  strrpos($src, '.'));

        if (empty($type)) {
            $name = $prefix . $last;
        } else {
            $name = $prefix . '_' . $type . $last;
        }
        return $name;
    }

    public function getDirectoryPath($baseDirectory, $id) {
        $i = 0;

        $path = rtrim($baseDirectory, '/') . '/';
        $id = strval($id);

        while ($i < strlen($id) ) {
            $path .= $id[$i] . '/';
            $i++;
        }

        return $path;
    }

    public function makeDirectoriesForPath($baseDirectory, $id)
    {

        $dir = rtrim($baseDirectory, '/') . '/';
        $path = $this->getDirectoryPath($dir, $id);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    public function makeVideoDirectories($object, $id = null)
    {
        $dir = $this->getBaseVideoDir($object);
        $path = $this->makeDirectoriesForPath($dir, $id ? $id : $object->getId());

        return $path;
    }

    public function getVideosPath()
    {
        return [
            Day::class => [
                'path' => $this->container->getParameter('app.day_video_path'),
                'dir' => $this->container->getParameter('app.day_video_directory'),
            ]
        ];
    }

    public function getVideoPath($object)
    {
        $full = $this->getVideoDir($object);
        $base = $this->getBaseVideoPath($object);
        return substr($full, strpos($full, $base));
    }

    public function getBaseVideoPath($object)
    {
        $paths = $this->getVideosPath();
        $className = $this->getRealEntityClass($object);
        return $paths[$className]['path'];
    }

    protected function getRealEntityClass($object)
    {
        return ClassUtils::getClass($object);
    }

    public function getVideoDir($object)
    {
        return $this->makeVideoDirectories($object);
    }

    public function getBaseVideoDir($object)
    {
        $paths = $this->getVideosPath();
        $className = $this->getRealEntityClass($object);
        return $paths[$className]['dir'];
    }

    public function getVideoLink($object, $type='')
    {
        $VideoName = $this->getName($object->getVideoName(), $type);

        $dir = $this->getVideoPath($object);

        $path = $dir . $VideoName;
        if (strpos($path, '.') === false) {
            return null;
        }

        return $path;
    }

    protected function removeDirectory($path) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
        return;
    }

    public function removeVideo($entity, $entityId)
    {
        if (empty($directory)) {
            $directory = $this->makeVideoDirectories($entity, $entityId);
        }
            $file = $directory . $entity->getVideoName();
            if (is_file($file)) {
                unlink($file);
            }
    }

    public function makeVideoForFile($entity, $file)
    {
        if (!is_file($file)) {
            throw new \Exception($file . ' doesn\'t exist');
        }

        copy($file,
            $this->getVideoDir($entity) . $entity->getVideoName());
    }

    public function makeVideo($tempDir, $entity, $removeTempVideo=false)
    {
        $tempDir = rtrim($tempDir, '/') . '/';
        $file = $tempDir . $entity->getVideoName();
        
        copy($file,
            $this->getVideoDir($entity) . $entity->getVideoName());

        if ($removeTempVideo) {
            unlink($file);
        }
    }
}