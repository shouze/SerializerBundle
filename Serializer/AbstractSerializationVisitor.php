<?php

namespace JMS\SerializerBundle\Serializer;

abstract class AbstractSerializationVisitor implements VisitorInterface
{
    private $navigator;

    public function __construct(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
    }

    public function visitString($data, $type)
    {
        return $data;
    }

    public function visitBoolean($data, $type)
    {
        return $data;
    }

    public function visitDouble($data, $type)
    {
        return $data;
    }

    public function visitInteger($data, $type)
    {
        return $data;
    }
}