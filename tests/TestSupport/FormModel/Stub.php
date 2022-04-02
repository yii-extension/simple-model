<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel;

use Yii\Extension\FormModel\FormModel;

final class Stub extends FormModel
{
    public string $public = '';
    protected string $protected = '';
    private static string $static  = '';

    public function getName(): string
    {
        return 'Stub';
    }
}
