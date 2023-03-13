<?php

namespace MissaelAnda\Url;

class QueryParameter implements \Stringable, \ArrayAccess
{
    /**
     * @var array<string>
     */
    protected array $values = [];

    public function __construct(
        protected string $name,
        array|string|null $value = [],
        public bool $urlencoded = false,
        public bool $enumerated = true,
    ) {
        $this->add($value);
    }

    public function add(array|string|null $value): static
    {
        if (!empty($value)) {
            if (is_array($value)) {
                $this->values = array_merge($this->values, $value);
            } else {
                $this->values[] = $value;
            }
        }

        return $this;
    }

    public function splice(int $offset, $length = null)
    {
        array_splice($this->values, $offset, $length);
    }

    public function pop(): ?string
    {
        return array_pop($this->values);
    }

    public function __toString(): string
    {
        $encoding = $this->encoding();
        if ($this->enumerated) {
            return http_build_query([$this->name => $this->values], '', '&', $encoding);
        } else {
            $values = array_map(fn ($value) => http_build_query([$this->name => $value], '', '&', $encoding), $this->values);

            return implode('&', $values);
        }
        $values = array_map(fn ($value) => $this->name, $this->values);
    }

    public function values(): array
    {
        return $this->values;
    }

    public function withValues(array $values): static
    {
        $this->values = $values;
        return $this;
    }

    protected function encoding(): int
    {
        return $this->urlencoded ? PHP_QUERY_RFC1738 : PHP_QUERY_RFC3986;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }


    public function offsetGet($offset): string
    {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
