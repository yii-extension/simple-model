<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Validator\Rule\Ip;

final class IpRule extends FormModel
{
    #[Ip]
    private string $ip = '';
}
