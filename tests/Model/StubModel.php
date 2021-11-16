<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Model;

use Yii\Extension\Simple\Model\BaseModel;

final class StubModel extends BaseModel
{
    public function getName(): string
    {
        return 'StubModel';
    }
}
