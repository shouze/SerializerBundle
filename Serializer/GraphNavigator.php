<?php

namespace JMS\SerializerBundle\Serializer;

use Metadata\MetadataFactoryInterface;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyInterface;

final class GraphNavigator
{
    private $exclusionStrategy;
    private $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function setExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy)
    {
        $this->exclusionStrategy = $exclusionStrategy;
    }

    public function accept($data, $type, VisitorInterface $visitor)
    {
        // determine type if not given
        if (null === $type) {
            if (null === $data) {
                return;
            }

            $type = gettype($data);
            if ('object' === $type) {
                if ($data instanceof \Traversable) {
                    $type = 'traversable';
                } else {
                    $type = get_class($data);
                }
            }
        }

        // try custom handler
        $rs = $visitor->visitUsingCustomHandler($data, $type, $handled);
        if ($handled) {
            return $rs;
        }

        if ('string' === $type) {
            return $visitor->visitString($data, $type);
        } else if ('integer' === $type) {
            return $visitor->visitInteger($data, $type);
        } else if ('boolean' === $type) {
            return $visitor->visitBoolean($data, $type);
        } else if ('double' === $type) {
            return $visitor->visitDouble($data, $type);
        } else if ('array' === $type || 0 === strpos($type, 'array')) {
            return $visitor->visitArray($data, $type);
        } else if ('traversable' === $type) {
            return $visitor->visitTraversable($data, $type);
        } else {
            $metadata = $this->metadataFactory->getMetadataForClass($type);
            if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipClass($metadata)) {
                return;
            }

            if (!$visitor->startVisitingObject($data, $type)) {
                return;
            }

            foreach ($metadata->classMetadata as $classMetadata) {
                foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
                    if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipProperty($propertyMetadata)) {
                        continue;
                    }

                    // try custom handler
                    if (!$visitor->visitPropertyUsingCustomHandler($propertyMetadata, $data)) {
                        $visitor->visitProperty($propertyMetadata, $data);
                    }
                }
            }

            return $visitor->endVisitingObject($data, $type);
        }
    }
}