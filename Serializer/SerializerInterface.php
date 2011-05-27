<?php

namespace JMS\SerializerBundle\Serializer;

interface SerializerInterface
{
    function serialize($data, SerializationContextInterface $context);
}