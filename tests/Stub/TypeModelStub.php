<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Stub;

use Yii\Extension\Simple\Model\BaseModel;

final class TypeModelStub extends BaseModel
{
    private array $array = [];
    private bool $bool = false;
    private float $float = 0;
    private int $int = 0;
    private ?object $object = null;
    private string $string = '';
    private string $toCamelCase = '';
}
