<?php

declare(strict_types=1);

namespace Lichi\Report;

abstract class Validator
{
    abstract public function validate(Pipeline $pipeline): bool;
}