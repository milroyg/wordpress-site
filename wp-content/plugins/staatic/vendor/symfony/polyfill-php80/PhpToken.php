<?php

namespace Symfony\Polyfill\Php80;

class PhpToken
{
    public $id;
    public $text;
    public $line;
    public $pos;
    public function __construct(int $id, string $text, int $line = -1, int $position = -1)
    {
        $this->id = $id;
        $this->text = $text;
        $this->line = $line;
        $this->pos = $position;
    }
    public function getTokenName(): ?string
    {
        if ('UNKNOWN' === $name = token_name($this->id)) {
            $name = \strlen($this->text) > 1 || \ord($this->text) < 32 ? null : $this->text;
        }
        return $name;
    }
    public function is($kind): bool
    {
        foreach ((array) $kind as $value) {
            if (\in_array($value, [$this->id, $this->text], \true)) {
                return \true;
            }
        }
        return \false;
    }
    public function isIgnorable(): bool
    {
        return \in_array($this->id, [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT, \T_OPEN_TAG], \true);
    }
    public function __toString(): string
    {
        return (string) $this->text;
    }
    /**
     * @param string $code
     * @param int $flags
     */
    public static function tokenize($code, $flags = 0): array
    {
        $line = 1;
        $position = 0;
        $tokens = token_get_all($code, $flags);
        foreach ($tokens as $index => $token) {
            if (\is_string($token)) {
                $id = \ord($token);
                $text = $token;
            } else {
                [$id, $text, $line] = $token;
            }
            $tokens[$index] = new static($id, $text, $line, $position);
            $position += \strlen($text);
        }
        return $tokens;
    }
}
