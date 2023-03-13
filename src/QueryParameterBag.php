<?php

namespace MissaelAnda\Url;

class QueryParameterBag implements \Stringable
{
    /**
     * @param  array<string,QueryParameter> $parameters
     */
    public function __construct(
        protected array $parameters = [],
        protected bool $urlencoded = false,
        protected bool $enumerated = true,
    ) {
        // 
    }

    public static function parse(string $query = '', bool $urlencoded = false, bool $enumerated = true): static
    {
        if ($query === '') {
            return new static();
        }

        $parts = explode('&', $query);
        $parameters = [];

        foreach ($parts as $part) {
            $values = explode('=', $part, 2);
            $key = $values[0];
            $value = $values[1] ?? null;

            if (isset($parameters[$key])) {
                $parameters[$key]->add($value);
            } else {
                $parameters[$key] = new QueryParameter($key, $value, $urlencoded, $enumerated);
            }
        }

        return new static($parameters, $urlencoded, $enumerated);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->parameters[$key];
        }

        return is_callable($default) ? $default() : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    public function set(string $key, string|array $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function add(string $key, string $value): self
    {
        if (isset($this->parameters[$key])) {
            $this->parameters[$key]->add($value);
        } else {
            $this->parameters[$key] = new QueryParameter($key, [$value]);
        }

        return $this;
    }

    public function unset(string $key): self
    {
        unset($this->parameters[$key]);

        return $this;
    }

    public function unsetAll(): self
    {
        $this->parameters = [];

        return $this;
    }

    public function all(): array
    {
        return $this->parameters;
    }

    public function getUrlencoded(): bool
    {
        return $this->urlencoded;
    }

    public function getEnumerated(): bool
    {
        return $this->urlencoded;
    }

    public function setUrlencoded(bool $urlencoded = true): static
    {
        $this->urlencoded = $urlencoded;
        foreach ($this->parameters as $_ => $parameter) {
            $parameter->urlencoded = $urlencoded;
        }
        return $this;
    }

    public function setEnumerated(bool $enumerated = true): static
    {
        $this->enumerated = $enumerated;
        foreach ($this->parameters as $_ => $parameter) {
            $parameter->enumerated = $enumerated;
        }
        return $this;
    }

    public function __toString(): string
    {
        return implode('&', $this->parameters);
    }
}
