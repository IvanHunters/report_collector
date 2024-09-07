<?php

declare(strict_types=1);

namespace Lichi\Report\test;

use Lichi\Report\Pipeline;
use Lichi\Report\Validator;

class TestValidator extends Validator
{
    public function validate(Pipeline $pipeline): bool
    {
        $pipelineData = $pipeline->getData();
        if (!isset($pipelineData['test'])) {
            $pipeline->addError('Test not exists');
            return false;
        }
        return true;
    }
}