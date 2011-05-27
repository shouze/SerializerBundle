<?php

namespace JMS\SerializerBundle\Tests\Serializer;

use JMS\SerializerBundle\Tests\Fixtures\SimpleObject;

use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use Metadata\MetadataFactory;
use JMS\SerializerBundle\Serializer\GraphNavigator;
use JMS\SerializerBundle\Serializer\GenericSerializationVisitor;

class GenericSerializationVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleObject()
    {
        $obj = new SimpleObject('foo', 'bar');
        list($visitor, $navigator) = $this->getVisitor();

        $this->assertEquals(json_encode(array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'boo')), $visitor->getResult());
    }

    private function getVisitor()
    {
        $navigator = new GraphNavigator(new MetadataFactory(new AnnotationDriver(new AnnotationReader())));
        $visitor = new GenericSerializationVisitor($navigator, new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy()));

        return array($visitor, $navigator);
    }
}