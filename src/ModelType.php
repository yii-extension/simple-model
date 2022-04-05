<?php

declare(strict_types=1);

namespace Yii\Extension\Model;

use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use Stringable;
use Yii\Extension\Model\Contract\ModelContract;

use function array_key_exists;

final class ModelType
{
    private array $attributes;

    public function __construct(private ModelContract $model)
    {
        $this->attributes = $this->collectAttributes();
    }

    public function getAttribute(string $attribute): string
    {
        return match (isset($this->attributes[$attribute]) && is_string($this->attributes[$attribute])) {
            true => $this->attributes[$attribute],
            false => '',
        };
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns the type of the given value.
     */
    public function phpTypeCast(string $name, mixed $value): mixed
    {
        try {
            return match ($this->attributes[$name]) {
                'bool' => (bool) $value,
                'float' => (float) $value,
                'int' => (int) $value,
                'string' => (string) $value,
                default => $value,
            };
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                sprintf('The value is not of type "%s".', (string) $this->attributes[$name])
            );
        }
    }

    /**
     * Returns the list of attribute types indexed by attribute names.
     *
     * By default, this method returns all non-static properties of the class.
     *
     * @return array list of attribute types indexed by attribute names.
     *
     * @psalm-suppress UndefinedClass
     */
    private function collectAttributes(): array
    {
        $class = new ReflectionClass($this->model);
        $attributes = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic() === false) {
                /** @var ReflectionNamedType|null $type */
                $type = $property->getType();
                $attributes[$property->getName()] = $type !== null ? $type->getName() : '';
            }
        }

        return $attributes;
    }
}
