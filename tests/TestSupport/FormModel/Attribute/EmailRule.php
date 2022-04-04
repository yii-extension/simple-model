<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\Model\FormModel;
use Yiisoft\Validator\Rule\Email;

final class EmailRule extends FormModel
{
    #[Email]
    private string $email = '';
}
