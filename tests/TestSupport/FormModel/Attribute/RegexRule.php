<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Validator\Rule\Regex;

final class RegexRule extends FormModel
{
    #[Regex(pattern: '/\w+/')]
    private string $regex = '';
}
