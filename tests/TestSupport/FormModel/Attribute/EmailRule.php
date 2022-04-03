<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Validator\Rule\Email;

final class EmailRule extends FormModel
{
    #[Email]
    private string $email = '';
}
