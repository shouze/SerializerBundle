<?php

namespace JMS\SerializerBundle\Serializer;

interface SerializationContextInterface
{
    function setVisitor(VisitorInterface $visitor);
    function serialize($data);
    function finalize();
}