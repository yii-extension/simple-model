<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\Model\FormModel;
use Yiisoft\Validator\Rule\Number;

final class NumberRule extends FormModel
{
    #[Number(min: 0, max: 10)]
    private ?int $number = null;
}
