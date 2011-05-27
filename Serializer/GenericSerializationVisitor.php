<?php

namespace JMS\SerializerBundle\Serializer;

use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;

final class GenericSerializationVisitor extends AbstractSerializationVisitor
{
    private $graphNavigator;
    private $namingStrategy;
    private $context;
    private $visited;
    private $root;
    private $dataStack;
    private $data;

    public function __construct(GraphNavigator $navigator, PropertyNamingStrategyInterface $namingStrategy)
    {
        $this->graphNavigator = $navigator;
        $this->namingStrategy = $namingStrategy;
        $this->visited = new \SplObjectStorage();
        $this->dataStack = new \SplStack();
    }

    public function visitString($data, $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return $data;
    }

    public function visitBoolean($data, $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return $data;
    }

    public function visitDouble($data, $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return $data;
    }

    public function visitArray($data, $type)
    {
        if (null === $this->root) {
            $this->root = array();
            $rs = &$this->root;
        } else {
            $rs = array();
        }

        foreach ($data as $k => $v) {
            $v = $this->graphNavigator->accept($v, null, $this);

            if (null === $v) {
                continue;
            }

            $rs[$k] = $v;
        }

        return $rs;
    }

    public function visitTraversable($data, $type)
    {
        if ($this->visited->contains($data)) {
            return null;
        }
        $this->visited->attach($data);

        return $this->visitArray($data, $type);
    }

    public function startVisitingObject($data, $type)
    {
        if ($this->visited->contains($data)) {
            return false;
        }

        if (null === $this->root) {
            $this->root = new \stdClass;
        }

        $this->visited->attach($data);
        $this->dataStack->push($this->data);
        $this->data = array();

        return true;
    }

    public function endVisitingObject($data, $type)
    {
        $rs = $this->data;
        $this->data = $this->dataStack->pop();

        if ($this->root instanceof \stdClass && 0 === $this->dataStack->count()) {
            $this->root = $rs;
        }

        return $rs;
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $v = $this->graphNavigator->accept($metadata->reflection->getValue($data), null, $this);
        $k = $this->namingStrategy->translateName($metadata);

        $this->data[$k] = $v;
    }

    public function visitUsingCustomHandler($data, $type, &$visited)
    {
        // TODO
        $visited = false;
    }

    public function visitPropertyUsingCustomHandler(PropertyMetadata $metadata, $object)
    {
        // TODO
        return false;
    }

    public function getResult()
    {
        return json_encode($this->root);
    }
}