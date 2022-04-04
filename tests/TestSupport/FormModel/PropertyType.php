<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel;

use Yii\Extension\Model\FormModel;

final class PropertyType extends FormModel
{
    private array $array = [];
    private bool $bool = false;
    private float $float = 0;
    private int $int = 0;
    private ?object $object = null;
    private string $string = '';
}
