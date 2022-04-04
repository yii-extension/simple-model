<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\Model\FormModel;
use Yiisoft\Validator\Rule\InRange;

final class InRangeRule extends FormModel
{
    #[InRange(range: [1, 10])]
    private int $inRange = 0;
}
