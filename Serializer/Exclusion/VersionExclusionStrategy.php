<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer\Exclusion;

use JMS\SerializerBundle\Metadata\PropertyMetadata;

class VersionExclusionStrategy implements ExclusionStrategyInterface
{
    private $version;

    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property)
    {
        if ((null !== $version = $property->sinceVersion) && version_compare($this->version, $version, '<')) {
            return true;
        }

        if ((null !== $version = $property->untilVersion) && version_compare($this->version, $version, '>')) {
            return true;
        }

        return false;
    }
}