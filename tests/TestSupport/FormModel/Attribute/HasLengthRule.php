<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\Model\FormModel;
use Yiisoft\Validator\Rule\HasLength;

final class HasLengthRule extends FormModel
{
    #[HasLength(min: 3, max: 6)]
    private string $hasLengthRule = '';
}
