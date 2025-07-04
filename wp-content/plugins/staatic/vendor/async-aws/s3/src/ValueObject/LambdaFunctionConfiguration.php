<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\Event;
final class LambdaFunctionConfiguration
{
    private $id;
    private $lambdaFunctionArn;
    private $events;
    private $filter;
    public function __construct(array $input)
    {
        $this->id = $input['Id'] ?? null;
        $this->lambdaFunctionArn = $input['LambdaFunctionArn'] ?? $this->throwException(new InvalidArgument('Missing required field "LambdaFunctionArn".'));
        $this->events = $input['Events'] ?? $this->throwException(new InvalidArgument('Missing required field "Events".'));
        $this->filter = isset($input['Filter']) ? NotificationConfigurationFilter::create($input['Filter']) : null;
    }
    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getEvents(): array
    {
        return $this->events;
    }
    public function getFilter(): ?NotificationConfigurationFilter
    {
        return $this->filter;
    }
    public function getId(): ?string
    {
        return $this->id;
    }
    public function getLambdaFunctionArn(): string
    {
        return $this->lambdaFunctionArn;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->id) {
            $node->appendChild($document->createElement('Id', $v));
        }
        $v = $this->lambdaFunctionArn;
        $node->appendChild($document->createElement('CloudFunction', $v));
        $v = $this->events;
        foreach ($v as $item) {
            if (!Event::exists($item)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "Event" for "%s". The value "%s" is not a valid "Event".', __CLASS__, $item));
            }
            $node->appendChild($document->createElement('Event', $item));
        }
        if (null !== $v = $this->filter) {
            $node->appendChild($child = $document->createElement('Filter'));
            $v->requestBody($child, $document);
        }
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
