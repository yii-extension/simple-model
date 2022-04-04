<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\Model\FormModel;
use Yiisoft\Validator\Rule\Required;

final class RequiredRule extends FormModel
{
    #[Required]
    private string $required = '';
}
