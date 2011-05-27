<?php

namespace JMS\SerializerBundle\Serializer;

use JMS\SerializerBundle\Metadata\PropertyMetadata;

interface VisitorInterface
{
    function visitString($data, $type);
    function visitBoolean($data, $type);
    function visitDouble($data, $type);
    function visitInteger($data, $type);
    function visitUsingCustomHandler($data, $type, &$visited);
    function visitArray($data, $type);
    function startVisitingObject($data, $type);
    function visitProperty(PropertyMetadata $metadata, $data);
    function endVisitingObject($data, $type);
    function visitPropertyUsingCustomHandler(PropertyMetadata $metadata, $object);
    function getResult();
}