<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;

final class ValidatorAttribute extends FormModel
{
    #[Required]
    private string $required = '';
}
