<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel;

use Yii\Extension\FormModel\FormModel;

final class Type extends FormModel
{
    private array $array = [];
    private bool $bool = false;
    private float $float = 0;
    private int $int = 0;
    private ?object $object = null;
    private string $string = '';
}