<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

class CombinedSelectorNode extends AbstractNode
{
    /**
     * @var NodeInterface
     */
    private $selector;
    /**
     * @var string
     */
    private $combinator;
    /**
     * @var NodeInterface
     */
    private $subSelector;
    public function __construct(NodeInterface $selector, string $combinator, NodeInterface $subSelector)
    {
        $this->selector = $selector;
        $this->combinator = $combinator;
        $this->subSelector = $subSelector;
    }
    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }
    public function getCombinator(): string
    {
        return $this->combinator;
    }
    public function getSubSelector(): NodeInterface
    {
        return $this->subSelector;
    }
    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus($this->subSelector->getSpecificity());
    }
    public function __toString(): string
    {
        $combinator = ' ' === $this->combinator ? '<followed>' : $this->combinator;
        return sprintf('%s[%s %s %s]', $this->getNodeName(), $this->selector, $combinator, $this->subSelector);
    }
}
