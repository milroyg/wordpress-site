<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\EncodingType;
use Staatic\Vendor\AsyncAws\S3\Enum\OptionalObjectAttributes;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
final class ListObjectsV2Request extends Input
{
    private $bucket;
    private $delimiter;
    private $encodingType;
    private $maxKeys;
    private $prefix;
    private $continuationToken;
    private $fetchOwner;
    private $startAfter;
    private $requestPayer;
    private $expectedBucketOwner;
    private $optionalObjectAttributes;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->delimiter = $input['Delimiter'] ?? null;
        $this->encodingType = $input['EncodingType'] ?? null;
        $this->maxKeys = $input['MaxKeys'] ?? null;
        $this->prefix = $input['Prefix'] ?? null;
        $this->continuationToken = $input['ContinuationToken'] ?? null;
        $this->fetchOwner = $input['FetchOwner'] ?? null;
        $this->startAfter = $input['StartAfter'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        $this->optionalObjectAttributes = $input['OptionalObjectAttributes'] ?? null;
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
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }
    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }
    public function getEncodingType(): ?string
    {
        return $this->encodingType;
    }
    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
    }
    public function getFetchOwner(): ?bool
    {
        return $this->fetchOwner;
    }
    public function getMaxKeys(): ?int
    {
        return $this->maxKeys;
    }
    public function getOptionalObjectAttributes(): array
    {
        return $this->optionalObjectAttributes ?? [];
    }
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }
    public function getRequestPayer(): ?string
    {
        return $this->requestPayer;
    }
    public function getStartAfter(): ?string
    {
        return $this->startAfter;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->requestPayer) {
            if (!RequestPayer::exists($this->requestPayer)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "RequestPayer" for "%s". The value "%s" is not a valid "RequestPayer".', __CLASS__, $this->requestPayer));
            }
            $headers['x-amz-request-payer'] = $this->requestPayer;
        }
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }
        if (null !== $this->optionalObjectAttributes) {
            $items = [];
            foreach ($this->optionalObjectAttributes as $value) {
                if (!OptionalObjectAttributes::exists($value)) {
                    throw new InvalidArgument(\sprintf('Invalid parameter "OptionalObjectAttributes" for "%s". The value "%s" is not a valid "OptionalObjectAttributes".', __CLASS__, $value));
                }
                $items[] = $value;
            }
            $headers['x-amz-optional-object-attributes'] = implode(',', $items);
        }
        $query = [];
        if (null !== $this->delimiter) {
            $query['delimiter'] = $this->delimiter;
        }
        if (null !== $this->encodingType) {
            if (!EncodingType::exists($this->encodingType)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "EncodingType" for "%s". The value "%s" is not a valid "EncodingType".', __CLASS__, $this->encodingType));
            }
            $query['encoding-type'] = $this->encodingType;
        }
        if (null !== $this->maxKeys) {
            $query['max-keys'] = (string) $this->maxKeys;
        }
        if (null !== $this->prefix) {
            $query['prefix'] = $this->prefix;
        }
        if (null !== $this->continuationToken) {
            $query['continuation-token'] = $this->continuationToken;
        }
        if (null !== $this->fetchOwner) {
            $query['fetch-owner'] = $this->fetchOwner ? 'true' : 'false';
        }
        if (null !== $this->startAfter) {
            $query['start-after'] = $this->startAfter;
        }
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(\sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']) . '?list-type=2';
        $body = '';
        return new Request('GET', $uriString, $query, $headers, StreamFactory::create($body));
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
     * @param string|null $value
     */
    public function setContinuationToken($value): self
    {
        $this->continuationToken = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setDelimiter($value): self
    {
        $this->delimiter = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setEncodingType($value): self
    {
        $this->encodingType = $value;
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
     * @param bool|null $value
     */
    public function setFetchOwner($value): self
    {
        $this->fetchOwner = $value;
        return $this;
    }
    /**
     * @param int|null $value
     */
    public function setMaxKeys($value): self
    {
        $this->maxKeys = $value;
        return $this;
    }
    /**
     * @param mixed[] $value
     */
    public function setOptionalObjectAttributes($value): self
    {
        $this->optionalObjectAttributes = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setPrefix($value): self
    {
        $this->prefix = $value;
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
    public function setStartAfter($value): self
    {
        $this->startAfter = $value;
        return $this;
    }
}
