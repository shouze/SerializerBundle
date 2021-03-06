<?php

namespace JMS\SerializerBundle\Tests\Serializer\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Serializer\Normalizer\NativePhpTypeNormalizer;
use JMS\SerializerBundle\Serializer\Serializer;
use JMS\SerializerBundle\Serializer\UnserializeInstanceCreator;
use Metadata\MetadataFactory;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Tests\Fixtures\AllExcludedObject;
use JMS\SerializerBundle\Tests\Fixtures\VersionedObject;
use JMS\SerializerBundle\Serializer\Exclusion\DisjunctExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\NoneExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\AllExclusionStrategy;
use JMS\SerializerBundle\Tests\Fixtures\CircularReferenceParent;
use JMS\SerializerBundle\Tests\Fixtures\SimpleObject;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyFactory;
use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\SerializerBundle\Serializer\Normalizer\PropertyBasedNormalizer;

class PropertyBasedNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeAllExcludedObject()
    {
        $object = new AllExcludedObject();
        $normalizer = $this->getNormalizer();

        $this->assertEquals(array('bar' => 'bar'), $normalizer->normalize($object));
    }

    public function testNormalizeVersionedObject()
    {
        $object = new VersionedObject('name1', 'name2');

        $normalizer = $this->getNormalizer();
        $this->assertEquals(array('name' => 'name2'), $normalizer->normalize($object, null));

        $normalizer = $this->getNormalizer('0.1.0');
        $this->assertEquals(array('name' => 'name1'), $normalizer->normalize($object, null));

        $normalizer = $this->getNormalizer('1.1.0');
        $this->assertEquals(array('name' => 'name2'), $normalizer->normalize($object, null));
    }

    public function testNormalizeCircularReference()
    {
        $normalizer = $this->getNormalizer();
        $object = new CircularReferenceParent();

        $this->assertEquals(array(
            'collection' => array(
                array('name' => 'child1'),
                array('name' => 'child2'),
            ),
            'another_collection' => array(
                array('name' => 'child1'),
                array('name' => 'child2'),
            ),
        ), $normalizer->normalize($object, null));
    }

    public function testNormalize()
    {
        $normalizer = $this->getNormalizer();
        $object = new SimpleObject('foo', 'bar');

        $this->assertEquals(array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'boo'), $normalizer->normalize($object, null));
    }

    protected function getNormalizer($version = null)
    {
        $driver = new AnnotationDriver(new AnnotationReader());
        $propertyNamingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy('_'));

        if (null === $version) {
            $strategies = array(
                'ALL'  => new AllExclusionStrategy($reader),
                'NONE' => new NoneExclusionStrategy($reader),
            );
        } else {
            $versionStrategy = new VersionExclusionStrategy($version);
            $strategies = array(
                'ALL'  => new DisjunctExclusionStrategy(array(
                    $versionStrategy, new AllExclusionStrategy($reader)
                )),
                'NONE' => new DisjunctExclusionStrategy(array(
                    $versionStrategy, new NoneExclusionStrategy($reader),
                )),
            );
        }
        $exclusionStrategyFactory = new ExclusionStrategyFactory($strategies);

        $normalizer = new PropertyBasedNormalizer(new MetadataFactory($driver), $propertyNamingStrategy, new UnserializeInstanceCreator(), $exclusionStrategyFactory);

        $serializer = new Serializer(
            new NativePhpTypeNormalizer(),
            $normalizer,
            array(),
            array()
        );

        return $normalizer;
    }
}