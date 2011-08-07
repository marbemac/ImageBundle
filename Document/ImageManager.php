<?php

namespace Marbemac\ImageBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class ImageManager
{
    protected $dm;
    protected $repository;
    protected $class;
    protected $m;
    protected $maxWidth = 2048; // the max width image we will accept
    protected $maxHeight = 2048; // the max height image we will accept
    protected $fileTypes = array('jpg','jpeg','gif','png'); // the accepted filetypes

    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->name;

        $this->m = $dm->getConnection()->selectDatabase($dm->getConfiguration()->getDefaultDB());
    }

    public function createImage()
    {
        $class = $this->class;
        $image = new $class;
        return $image;
    }

    public function deleteObject(Image $image, $andFlush = true)
    {
        $image->remove();

        if ($andFlush)
        {
            $this->dm->flush();
        }
    }

    public function updateImage(Image $image, $andFlush = true)
    {
        $this->dm->persist($image);

        if ($andFlush)
        {
            $this->dm->flush();
        }
        
        return $image;
    }

    public function findImageBy(array $criteria)
    {
        $qb = $this->dm->createQueryBuilder($this->class);

        foreach ($criteria as $field => $val)
        {
            $qb->field($field)->equals($val);
        }

        $query = $qb->getQuery();

        return $query->getSingleResult();
    }

    public function findImagesBy(array $criteria, array $inCriteria = array(), array $sorts = array(), $dateRange = null, $limit = null, $offset = 0)
    {
        $qb = $this->dm->createQueryBuilder($this->class);

        foreach ($criteria as $field => $val)
        {
            $qb->field($field)->equals($val);
        }

        foreach ($inCriteria as $field => $vals)
        {
            $vals = is_array($vals) ? $vals : array();
            $qb->field($field)->in($vals);
        }

        foreach ($sorts as $field => $order)
        {
            $qb->sort($field, $order);
        }

        if ($dateRange)
        {
            if (isset($dateRange['start']))
            {
                $qb->field($dateRange['target'])->gte(new \MongoDate(strtotime($dateRange['start'])));
            }

            if (isset($dateRange['end']))
            {
                $qb->field($dateRange['target'])->lte(new \MongoDate(strtotime($dateRange['end'])));
            }
        }

        if ($limit !== null && $offset !== null)
        {
            $qb->limit($limit)
               ->skip($offset);
        }

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function saveImage($imageLocation, $createdBy = null, $groupId = null, $type = null, $parentId = null, $isOriginal = false, $width = null, $height = null, $croBoxData = null)
    {
        $imagine = new Imagine();
        $image = $imagine->open($imageLocation);
        $imageSize = $image->getSize();

        // Are we cropping it?
        if (($width && $height) && ($imageSize->getWidth() > $width || $imageSize->getHeight() > $height))
        {
            $image = $image->thumbnail(new Box($width, $height));
            $imageSize = $image->getSize();
        }
        // If not, at least make sure the image is not too big.
        else if ($imageSize->getWidth() > $this->maxWidth || $imageSize->getHeight() > $this->maxHeight)
        {
            $image = $image->thumbnail(new Box($this->maxWidth, $this->maxHeight));
            $imageSize = $image->getSize();
        }

        // Add the extension if required
        $parts = explode('.', $imageLocation);
        if (count($parts) == 1)
        {
            $imageLocation = $imageLocation.'.'.$this->getExtension($imageLocation);
        }
        $image->save($imageLocation);

        $dbImage = $this->createImage();
        $dbImage->setWidth($width ? $width : $imageSize->getWidth());
        $dbImage->setHeight($height ? $height : $imageSize->getHeight());
        $dbImage->setExt($this->getExtension($imageLocation));

        if ($createdBy)
        {
            $dbImage->setCreatedBy($createdBy);
        }

        if ($groupId)
        {
            $dbImage->setGroupId($groupId);
        }
        else
        {
            $dbImage->setGroupId(new \MongoId());
        }

        if ($type)
        {
            $dbImage->setType($type);
        }

        if ($parentId)
        {
            $dbImage->setParentId($parentId);
        }

        if ($isOriginal)
        {
            $dbImage->setIsOriginal(true);
        }

        $dbImage->setFile($imageLocation);
        $this->updateImage($dbImage);

        return $dbImage;
    }

    public function findOrCreate($groupId, $w, $h)
    {
        $images = $this->findImagesBy(array('groupId' => new \MongoId($groupId)));

        $original = null;
        $imageFound = null;
        foreach ($images as $image)
        {
            if ($image->getWidth() == $w && $image->getHeight() == $h)
            {
                return $image;
            }
            else if ($image->getIsOriginal())
            {
                $original = $image;
            }
        }

        if (!$original)
        {
            return 'not found...';
        }

        $imagine = new Imagine();
        $newImage = $imagine->load($original->getFile()->getBytes());
        $tmpLocation = '/tmp/'.uniqid('i', true).'.'.$original->getExt();
        $newImage->save($tmpLocation);
        
        return $this->saveImage($tmpLocation, $original->getCreatedBy(), $original->getGroupId(), $original->getType(), $original->getParentId(), false, $w, $h, null);
    }

    public function getExtension($img)
    {
         // File extensions
        $imgInfo_array = getimagesize($img);
        $parts = explode('/', $imgInfo_array['mime']);
        $ext = $parts[count($parts)-1];
        return $ext;
    }

    public function getImageUrlData($groupId, $w, $h)
    {
        return base64_encode($groupId.'-'.$w.'-'.$h);
    }
}
