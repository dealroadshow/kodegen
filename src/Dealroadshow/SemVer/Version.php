<?php

namespace Dealroadshow\SemVer;

class Version
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function alpha(): bool
    {
        return \str_contains($this->value, 'alpha');
    }

    public function beta(): bool
    {
        return \str_contains($this->value, 'beta');
    }

    public function stable(): bool
    {
        return !($this->alpha() || $this->beta());
    }

    public function string(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
