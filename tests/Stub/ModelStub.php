<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Stub;

use Yii\Extension\Simple\Model\BaseModel;

final class ModelStub extends BaseModel
{
    public string $public;
    protected string $protected;
    private string $private;
    private static string $static;
}
