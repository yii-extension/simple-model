<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\Model\FormModel;
use Yiisoft\Validator\Rule\Regex;

final class RegexRule extends FormModel
{
    #[Regex(pattern: '/\w+/')]
    private string $regex = '';
}
