<?php

namespace AppBundle\Service;


use AppBundle\Entity\CompanyPage;
use AppBundle\Entity\Day;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Utils\ImageInterface;
use Doctrine\Common\Util\ClassUtils;
use Imagine\Image\Box;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Imagine\Gd\Imagine;
use AppBundle\Entity\UserMedia;

class ImageHelper
{
    const SIZE_THUMB = 40;
    const SIZE_AVATAR = 55;
    const SIZE_SMALL = 100;
    const SIZE_MIDDLE = 300;
    const SIZE_NORMAL = 500;
    const SIZE_LARGE = 700;

    private $imagine;
    private $container;


    public function __construct(ContainerInterface $container)
    {
        $this->imagine = new Imagine();
        $this->container = $container;

    }

    public function thumbnail(string $src, string $dest, int $size=40, $mode= \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND)
    {
        $this->load($src);
        $size = new Box($size, $size);
        $this->imagine
            ->open($src)
            ->thumbnail($size, $mode)
            ->save($dest);
    }


    private function load($src)
    {
        if (!is_file($src)) {
            throw new FileNotFoundException('File ' . $src . ' does\'nt exist');
        }
        if (!$this->isImage($src)) {
            throw new \InvalidArgumentException('File ' . $src . ' is not correct image');
        }

        $this->imagine->open($src);
        return $this;
    }

    public function isImage($src)
    {
        return exif_imagetype($src) != false;
    }

    public function getLink($object, $type = 'origin')
    {
        $baseDirectoryPath = $this->getBaseDirectory($object);
    }

    public function getSizes()
    {
        return [
            'thumb' => self::SIZE_THUMB,
            'avatar' => self::SIZE_AVATAR,
            'small' => self::SIZE_SMALL,
            'middle' => self::SIZE_MIDDLE,
            'normal' => self::SIZE_NORMAL,
            'large' => self::SIZE_LARGE
        ];
    }

    public function makeThumbnails(string $src)
    {
        $file = basename($src);
        $dir = rtrim(dirname($src), '/')  . '/';
        foreach ($this->getSizes() as $type => $size) {
            $name = $this->getName($file, $type);
            $this->thumbnail($src, $dir . $name, $size);
        }
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

    public function makeImageDirectories($object, $id = null)
    {
        $dir = $this->getBaseImageDir($object);
        $path = $this->makeDirectoriesForPath($dir, $id ? $id : $object->getId());

        return $path;
    }

    public function makeThumbnail(string $src, string $dest, int $height=40, $width=40, $mode= \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND)
    {
        $this->load($src);
        
        list($w, $h) = getimagesize($src);

        $size = new Box($height, $width);


        $image = $this->imagine
            ->open($src);

        if ($w >= $width || $h >= $height) {
            $image = $image->thumbnail($size, $mode);
        } else {
            $image = $image->resize($size);
        }

        $image->save($dest);
    }

    public function getImagesPath()
    {
        return [
            Post::class => [
                'path' => $this->container->getParameter('app.post_image_path'),
                'dir' => $this->container->getParameter('app.post_image_directory'),
            ],
            Feedback::class => [
                'path' => $this->container->getParameter('app.feedback_image_path'),
                'dir' => $this->container->getParameter('app.feedback_image_directory'),
            ],

            Day::class => [
                'path' => $this->container->getParameter('app.day_image_path'),
                'dir' => $this->container->getParameter('app.day_image_directory'),
            ],

            CompanyPage::class => [
                'path' => $this->container->getParameter('app.company_page_path'),
                'dir' => $this->container->getParameter('app.company_page_image_directory'),
            ],
        ];
    }

    public function getImagePath($object)
    {
        $full = $this->getImageDir($object);
        $base = $this->getBaseImagePath($object);
        return substr($full, strpos($full, $base));
    }

    public function getBaseImagePath($object)
    {
        $paths = $this->getImagesPath();
        $className = $this->getRealEntityClass($object);
        return $paths[$className]['path'];
    }

    protected function getRealEntityClass($object)
    {
        return ClassUtils::getClass($object);
    }

    public function getImageDir($object)
    {
        return $this->makeImageDirectories($object);
    }

    public function getBaseImageDir($object)
    {
        $paths = $this->getImagesPath();
        $className = $this->getRealEntityClass($object);
        return $paths[$className]['dir'];
    }


    public function makeBox(string $src, string $dest, int $size=40, $mode= \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND)
    {
        $this->makeThumbnail($src, $dest, $size, $size, $mode);
    }

    public function makeImageForFile($entity, $file)
    {
        if (!is_file($file)) {
            throw new \Exception($file . ' doesn\'t exist');
        }

        copy($file,
            $this->getImageDir($entity) . $entity->getImageName());
        $this->makeCollection($entity);
    }

    public function makeCollection(ImageInterface $object, $directory='')
    {
        if (!in_array(get_class($object), array_keys($this->getImagesPath()))) {
            throw new \InvalidArgumentException('Wrong type object for image');
        }


        $imageName = $object->getImageName();

        if (empty($directory)) {
            $directory = $this->makeImageDirectories($object);
        }

        $directory = rtrim($directory, '/') . '/';

        foreach ($this->getSizes() as $type => $size) {
            $name = $this->getName($imageName, $type);
            $this->makeBox($directory . $imageName, $directory . $name, $size);
        }
    }

    public function removeCollection(ImageInterface $object, $directory='')
    {
        if (!in_array(get_class($object), array_keys($this->getImagesPath()))) {
            throw new \InvalidArgumentException('Wrong type object for image');
        }
        $imageName = $object->getImageName();

        if (empty($directory)) {
            $directory = $this->makeImageDirectories($object);
        }

        $directory = rtrim($directory, '/') . '/';
        $paths = $this->getSizes();
        $paths[] = ['' => $imageName];

        foreach ($paths as $type => $size) {
            $file = $directory . $this->getName($imageName, $type);
            if (is_file($file)) {
                unlink($file);
            }
        }

        return $paths;
    }

    public function getImageLink($object, $type='')
    {
        $imageName = $this->getName($object->getImageName(), $type);
        
        $dir = $this->getImagePath($object);
        
        $path = $dir . $imageName;
        if (strpos($path, '.') === false) {
            return null;
        }

        return $path;
    }


}