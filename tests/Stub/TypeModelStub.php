<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Stub;

use Yii\Extension\Simple\Model\AbstractModel;

final class TypeModelStub extends AbstractModel
{
    private array $array;
    private bool $bool;
    private float $float;
    private int $int;
    private object $object;
    private string $string;
}
