<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\FormModel;

use Yii\Extension\Simple\Model\FormModel;

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
