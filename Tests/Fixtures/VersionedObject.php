<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\SerializedName;
use JMS\SerializerBundle\Annotation\Since;
use JMS\SerializerBundle\Annotation\Until;

class VersionedObject
{
    /**
     * @Until("1.0.0")
     */
    private $name;

    /**
     * @Since("1.0.1")
     * @SerializedName("name")
     */
    private $name2;

    public function __construct($name, $name2)
    {
        $this->name  = $name;
        $this->name2 = $name2;
    }
}