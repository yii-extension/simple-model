<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Validator\Rule\InRange;

final class InRangeRule extends FormModel
{
    #[InRange(range: [1, 10])]
    private int $inRange = 0;
}
