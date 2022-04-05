<?php

declare(strict_types=1);

namespace Yii\Extension\Model;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use Yii\Extension\Model\Contract\FormErrorsContract;
use Yii\Extension\Model\Contract\ModelContract;
use Yiisoft\Strings\Inflector;
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidatorInterface;

use function array_key_exists;
use function array_keys;
use function explode;
use function is_subclass_of;
use function property_exists;
use function str_contains;
use function strrchr;
use function substr;

abstract class Model implements ModelContract
{
    private array $attributes;
    private ?FormErrorsContract $formErrors = null;
    private ?Inflector $inflector = null;
    private array $rawData = [];
    private ?ValidatorInterface $validator = null;

    public function __construct()
    {
        $this->attributes = $this->collectAttributes();
    }

    public function attributes(): array
    {
        return array_keys($this->attributes);
    }

    public function error(): FormErrorsContract
    {
        return match (empty($this->formErrors)) {
            true => $this->formErrors = new FormErrors(),
            false => $this->formErrors,
        };
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->rawData[$attribute] ?? $this->getCastValue($attribute);
    }

    public function getCastValue(string $attribute): mixed
    {
        return $this->readProperty($attribute);
    }

    /**
     * @return string Returns classname without a namespace part or empty string when class is anonymous
     */
    public function getFormName(): string
    {
        if (str_contains(static::class, '@anonymous')) {
            return '';
        }

        $className = strrchr(static::class, '\\');
        if ($className === false) {
            return static::class;
        }

        return substr($className, 1);
    }

    public function getRules(): array
    {
        return [];
    }

    public function getRulesWithAttributes(): iterable
    {
        $attributeDataSet = new AttributeDataSet($this, $this->rawData);
        return $attributeDataSet->getRules();
    }

    public function has(string $attribute): bool
    {
        [$attribute, $nested] = $this->getNested($attribute);

        return $nested !== null || array_key_exists($attribute, $this->attributes);
    }

    public function isEmpty(): bool
    {
        return empty($this->rawData);
    }

    /**
     * @param array $data
     * @param string|null $formName
     *
     * @return bool
     */
    public function load(array $data, ?string $formName = null): bool
    {
        $this->error()->clear();

        $this->rawData = [];
        $scope = $formName ?? $this->getFormName();

        /** @psalm-var array<string, string> */
        $this->rawData = match (empty($scope)) {
            true => $data,
            false => $data[$scope] ?? [],
        };

        foreach ($this->rawData as $name => $value) {
            $this->setValue($name, $value);
        }

        return $this->rawData !== [];
    }

    public function setFormErrors(FormErrorsContract $formErrors): void
    {
        $this->formErrors = $formErrors;
    }

    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    public function setValue(string $name, mixed $value): void
    {
        [$realName] = $this->getNested($name);

        if (isset($this->attributes[$realName])) {
            /** @var mixed */
            $value = match ($this->attributes[$realName]) {
                'bool' => (bool) $value,
                'float' => (float) $value,
                'int' => (int) $value,
                'string' => (string) $value,
                default => $value,
            };

            $this->writeProperty($name, $value);
        }
    }

    public function setValues(array $data): void
    {
        /**
         * @var array<string, mixed> $data
         * @var mixed $value
         */
        foreach ($data as $name => $value) {
            $name = $this->getInflector()->toCamelCase($name);

            if ($this->has($name)) {
                $this->setValue($name, $value);
            } else {
                throw new InvalidArgumentException(sprintf('Attribute "%s" does not exist', $name));
            }
        }
    }

    public function validate(): bool
    {
        /** @psalm-var iterable<string, array<array-key, Rule>> */
        $rules = $this->getRulesWithAttributes();
        $result = $this->validator()->validate($this, $rules);
        $this->addErrors($result);
        return $result->isValid();
    }

    public function validator(): ValidatorInterface
    {
        return match (empty($this->validator)) {
            true => $this->validator = new FormValidator(),
            false => $this->validator,
        };
    }

    /**
     * Returns the list of attribute types indexed by attribute names.
     *
     * By default, this method returns all non-static properties of the class.
     *
     * @return array list of attribute types indexed by attribute names.
     */
    protected function collectAttributes(): array
    {
        $class = new ReflectionClass($this);
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

    protected function getInflector(): Inflector
    {
        return match (empty($this->inflector)) {
            true => $this->inflector = new Inflector(),
            false => $this->inflector,
        };
    }

    protected function getNestedValue(string $method, string $attribute): string
    {
        $result = '';

        [$attribute, $nested] = $this->getNested($attribute);

        if ($nested !== null) {
            /** @var ModelContract $attributeNestedValue */
            $attributeNestedValue = $this->getCastValue($attribute);
            /** @var string */
            $result = $attributeNestedValue->$method($nested);
        }

        return $result;
    }

    private function addErrors(Result $result): void
    {
        foreach ($result->getErrorMessagesIndexedByAttribute() as $attribute => $errors) {
            if ($this->has($attribute)) {
                foreach ($errors as $error) {
                    $this->error()->add($attribute, $error);
                }
            }
        }
    }

    /**
     * @return string[]
     *
     * @psalm-return array{0: string, 1: null|string}
     */
    private function getNested(string $attribute): array
    {
        if (!str_contains($attribute, '.')) {
            return [$attribute, null];
        }

        [$attribute, $nested] = explode('.', $attribute, 2);

        /** @var string */
        $attributeNested = $this->attributes[$attribute] ?? '';

        if (!is_subclass_of($attributeNested, self::class)) {
            throw new InvalidArgumentException("Attribute \"$attribute\" is not a nested attribute.");
        }

        if (!property_exists($attributeNested, $nested)) {
            throw new InvalidArgumentException("Undefined property: \"$attributeNested::$nested\".");
        }

        return [$attribute, $nested];
    }

    private function readProperty(string $attribute): mixed
    {
        $class = static::class;

        [$attribute, $nested] = $this->getNested($attribute);

        if (!property_exists($class, $attribute)) {
            throw new InvalidArgumentException("Undefined property: \"$class::$attribute\".");
        }

        /** @psalm-suppress MixedMethodCall */
        $getter = static function (ModelContract $class, string $attribute, ?string $nested): mixed {
            return match ($nested) {
                null => $class->$attribute,
                default => $class->$attribute->getCastValue($nested),
            };
        };

        $getter = Closure::bind($getter, null, $this);

        /** @var Closure $getter */
        return $getter($this, $attribute, $nested);
    }

    private function writeProperty(string $attribute, mixed $value): void
    {
        [$attribute, $nested] = $this->getNested($attribute);

        /** @psalm-suppress MixedMethodCall */
        $setter = static function (ModelContract $class, string $attribute, mixed $value, ?string $nested): void {
            match ($nested) {
                null => $class->$attribute = $value,
                default => $class->$attribute->setValue($nested, $value),
            };
        };

        $setter = Closure::bind($setter, null, $this);

        /** @var Closure $setter */
        $setter($this, $attribute, $value, $nested);
    }
}
