<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel;

use Yii\Extension\Model\FormModel;

final class PropertyVisibility extends FormModel
{
    public string $public = '';
    protected string $protected = '';
    private string $private = '';
    private static string $static = '';

    public function getName(): string
    {
        return 'Stub';
    }
}
