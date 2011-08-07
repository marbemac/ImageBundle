<?php

namespace Marbemac\ImageBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 * @MongoDB\Index(keys={"width"="asc", "height"="asc"})
 */
class Image
{
    /** @MongoDB\Id */
    protected $id;

    /**
     * @MongoDB\Field(type="object_id")
     * @MongoDB\Index(order="asc")
     */
    protected $groupId;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index(order="asc")
     */
    protected $type;

    /**
     * @MongoDB\Field(type="object_id")
     * @MongoDB\Index(order="asc")
     */
    protected $parentId;

    /** @MongoDB\File */
    protected $file;

    /** @MongoDB\Field(type="int") */
    protected $length;

    /** @MongoDB\Field(type="int") */
    protected $chunkSize;

    /** @MongoDB\Field(type="string") */
    protected $md5;

    /** @MongoDB\Field(type="string") */
    protected $ext;

    /** @MongoDB\Field(type="int") */
    protected $width;

    /** @MongoDB\Field(type="int") */
    protected $height;

    /** @MongoDB\Field(type="boolean") */
    protected $isOriginal;

    /** @MongoDB\Field(type="date") */
    protected $createdAt;

    /**
     * @MongoDB\Field(type="object_id")
     * @MongoDB\Index(order="asc")
     */
    protected $createdBy;

    public function __construct() {
    }

    /**
     * @return MongoId $id
     */
    public function getId()
    {
        return new \MongoId($this->id);
    }

    public function getGroupId()
    {
        return new \MongoId($this->groupId);
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getParentId()
    {
        return new \MongoId($this->parentId);
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getExt()
    {
        return $this->ext;
    }

    public function setExt($ext)
    {
        $this->ext = $ext;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getIsOriginal()
    {
        return $this->isOriginal;
    }

    public function setIsOriginal($isOriginal)
    {
        $this->isOriginal = $isOriginal;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @MongoDB\prePersist
     */
    public function touchCreated()
    {
        $this->createdAt = new \DateTime();
    }
}