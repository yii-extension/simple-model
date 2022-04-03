<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Validator\Rule\Number;

final class NumberRule extends FormModel
{
    #[Number(min: 0, max: 10)]
    private ?int $number = null;
}
