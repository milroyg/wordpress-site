<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;

final class Bucket
{
    private $name;
    private $creationDate;
    private $bucketRegion;
    public function __construct(array $input)
    {
        $this->name = $input['Name'] ?? null;
        $this->creationDate = $input['CreationDate'] ?? null;
        $this->bucketRegion = $input['BucketRegion'] ?? null;
    }
    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getBucketRegion(): ?string
    {
        return $this->bucketRegion;
    }
    public function getCreationDate(): ?DateTimeImmutable
    {
        return $this->creationDate;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
}
