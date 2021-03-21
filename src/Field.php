<?php

namespace Flipp\Client;

class Field
{
    public string $name;
    public ?string $value = null;
    public array $styles = [];

    public function __construct(string $name, ?string $value, ?array $styles)
    {
        $this->name = $name;
        $this->value = $value;
        $this->styles = (array) $styles;
    }

    public function getDefinition(): array
    {
        return [
            'name' => $this->name,
            'value' =>  $this->value,
        ] + $this->styles;
    }
}
