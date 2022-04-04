<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Arrays\ArrayHelper;

final class Dynamic extends FormModel
{
    public function __construct(private array $attributes = [])
    {
    }

    public function has(string $attribute): bool
    {
        return ArrayHelper::keyExists($this->attributes, $attribute);
    }

    public function getValue(string $attribute): mixed
    {
        if ($this->has($attribute)) {
            return $this->attributes[$attribute];
        }

        return null;
    }

    public function set(string $name, $value): void
    {
        if ($this->has($name)) {
            $this->attributes[$name] = $value;
        }
    }
}
