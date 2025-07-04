<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;

final class AccessControlPolicy
{
    private $grants;
    private $owner;
    public function __construct(array $input)
    {
        $this->grants = isset($input['Grants']) ? array_map([Grant::class, 'create'], $input['Grants']) : null;
        $this->owner = isset($input['Owner']) ? Owner::create($input['Owner']) : null;
    }
    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getGrants(): array
    {
        return $this->grants ?? [];
    }
    public function getOwner(): ?Owner
    {
        return $this->owner;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->grants) {
            $node->appendChild($nodeList = $document->createElement('AccessControlList'));
            foreach ($v as $item) {
                $nodeList->appendChild($child = $document->createElement('Grant'));
                $item->requestBody($child, $document);
            }
        }
        if (null !== $v = $this->owner) {
            $node->appendChild($child = $document->createElement('Owner'));
            $v->requestBody($child, $document);
        }
    }
}
