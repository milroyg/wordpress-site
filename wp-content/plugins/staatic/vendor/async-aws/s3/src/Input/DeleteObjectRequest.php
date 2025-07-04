<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
final class DeleteObjectRequest extends Input
{
    private $bucket;
    private $key;
    private $mfa;
    private $versionId;
    private $requestPayer;
    private $bypassGovernanceRetention;
    private $expectedBucketOwner;
    private $ifMatch;
    private $ifMatchLastModifiedTime;
    private $ifMatchSize;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->mfa = $input['MFA'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->bypassGovernanceRetention = $input['BypassGovernanceRetention'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        $this->ifMatch = $input['IfMatch'] ?? null;
        $this->ifMatchLastModifiedTime = !isset($input['IfMatchLastModifiedTime']) ? null : ($input['IfMatchLastModifiedTime'] instanceof DateTimeImmutable ? $input['IfMatchLastModifiedTime'] : new DateTimeImmutable($input['IfMatchLastModifiedTime']));
        $this->ifMatchSize = $input['IfMatchSize'] ?? null;
        parent::__construct($input);
    }
    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getBucket(): ?string
    {
        return $this->bucket;
    }
    public function getBypassGovernanceRetention(): ?bool
    {
        return $this->bypassGovernanceRetention;
    }
    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
    }
    public function getIfMatch(): ?string
    {
        return $this->ifMatch;
    }
    public function getIfMatchLastModifiedTime(): ?DateTimeImmutable
    {
        return $this->ifMatchLastModifiedTime;
    }
    public function getIfMatchSize(): ?int
    {
        return $this->ifMatchSize;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getMfa(): ?string
    {
        return $this->mfa;
    }
    public function getRequestPayer(): ?string
    {
        return $this->requestPayer;
    }
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->mfa) {
            $headers['x-amz-mfa'] = $this->mfa;
        }
        if (null !== $this->requestPayer) {
            if (!RequestPayer::exists($this->requestPayer)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "RequestPayer" for "%s". The value "%s" is not a valid "RequestPayer".', __CLASS__, $this->requestPayer));
            }
            $headers['x-amz-request-payer'] = $this->requestPayer;
        }
        if (null !== $this->bypassGovernanceRetention) {
            $headers['x-amz-bypass-governance-retention'] = $this->bypassGovernanceRetention ? 'true' : 'false';
        }
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }
        if (null !== $this->ifMatch) {
            $headers['If-Match'] = $this->ifMatch;
        }
        if (null !== $this->ifMatchLastModifiedTime) {
            $headers['x-amz-if-match-last-modified-time'] = $this->ifMatchLastModifiedTime->setTimezone(new DateTimeZone('GMT'))->format(DateTimeInterface::RFC7231);
        }
        if (null !== $this->ifMatchSize) {
            $headers['x-amz-if-match-size'] = (string) $this->ifMatchSize;
        }
        $query = [];
        if (null !== $this->versionId) {
            $query['versionId'] = $this->versionId;
        }
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(\sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        if (null === $v = $this->key) {
            throw new InvalidArgument(\sprintf('Missing parameter "Key" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Key'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']) . '/' . str_replace('%2F', '/', rawurlencode($uri['Key']));
        $body = '';
        return new Request('DELETE', $uriString, $query, $headers, StreamFactory::create($body));
    }
    /**
     * @param string|null $value
     */
    public function setBucket($value): self
    {
        $this->bucket = $value;
        return $this;
    }
    /**
     * @param bool|null $value
     */
    public function setBypassGovernanceRetention($value): self
    {
        $this->bypassGovernanceRetention = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setExpectedBucketOwner($value): self
    {
        $this->expectedBucketOwner = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setIfMatch($value): self
    {
        $this->ifMatch = $value;
        return $this;
    }
    /**
     * @param DateTimeImmutable|null $value
     */
    public function setIfMatchLastModifiedTime($value): self
    {
        $this->ifMatchLastModifiedTime = $value;
        return $this;
    }
    /**
     * @param int|null $value
     */
    public function setIfMatchSize($value): self
    {
        $this->ifMatchSize = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setKey($value): self
    {
        $this->key = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setMfa($value): self
    {
        $this->mfa = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setRequestPayer($value): self
    {
        $this->requestPayer = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setVersionId($value): self
    {
        $this->versionId = $value;
        return $this;
    }
}
