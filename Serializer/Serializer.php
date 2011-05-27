<?php

namespace JMS\SerializerBundle\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;

use Metadata\MetadataFactory;

class Serializer
{
    private $visitors;
    private $navigator;

    public function __construct(array $visitors)
    {
        $this->visitors = $visitors;
        $this->navigator = new GraphNavigator(new MetadataFactory(new AnnotationDriver(new AnnotationReader())));
    }

    public function serialize($data, $format)
    {
        $visitor = $this->getVisitor($format);

        return $this->navigator->accept($data, null, $visitor);
    }

    protected function getVisitor($format)
    {
        if (!isset($this->visitors[$format])) {
            throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }

        return $this->visitors[$format];
    }
}