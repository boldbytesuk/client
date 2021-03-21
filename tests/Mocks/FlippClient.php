<?php

namespace Flipp\Client\Tests\Mocks;

use Flipp\Client\Client;
use Illuminate\Support\Collection;

class FlippClient extends Client
{
    public function getFields(): Collection
    {
        return $this->fields;
    }
}
