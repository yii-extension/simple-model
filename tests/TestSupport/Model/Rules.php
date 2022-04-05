<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\Model;

use Yii\Extension\Model\Model;
use Yiisoft\Validator\Rule\Required;

final class Rules extends Model
{
    private string $firstName = '';
    private string $lastName = '';

    public function getRules(): array
    {
        return [
            'firstName' => [new Required()],
            'lastName' => [new Required()],
        ];
    }
}
