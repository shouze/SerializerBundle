<?php

namespace JMS\SerializerBundle\Serializer;

class XmlSerializationVisitor extends AbstractSerializationVisitor
{
    private $root;

    public function visitString($data, $type)
    {
        if (null === $this->root) {
            $this->root = $this->createDocument();
            $cData = $this->root->createCDATASection($data);
            $this->root->appendChild($cData);

            return $cData;
        }

        return $this->root->createCDATASection($data);
    }

    public function visitBoolean($data, $type)
    {
        if (null === $this->root) {
            $this->root = $this->createDocument();
            $boolean = $this->root->createTextNode((integer) $data);
            $this->root->appendChild($boolean);

            return $boolean;
        }

        return $this->root->createTextNode((integer) $data);
    }

    public function visitInteger($data, $type)
    {
        return $this->visitNumeric($data, $type);
    }

    public function visitDouble($data, $type)
    {
        return $this->visitNumeric($data, $type);
    }

    public function visitArray($data, $type)
    {
        if (null === $this->root) {
            $this->root = $this->createDocument();
            $isRoot = true;
        } else {
            $isRoot = false;
        }

        foreach ($data as $k => $v) {
            // attribute
            if ('@' === $k[0]) {
            }
        }
    }

    private function visitNumeric($data, $type)
    {
        if (null === $this->root) {
            $this->root = $this->createDocument();
            $numeric = $this->root->createTextNode((string) $data);
            $this->root->appendChild($numeric);

            return $numeric;
        }

        return $this->root->createTextNode((string) $data);
    }

    private function createDocument()
    {
        $doc = new \DOMDocument('1.0', 'utf-8');

        return $doc;
    }
}