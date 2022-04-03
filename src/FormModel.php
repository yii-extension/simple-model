<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use Yii\Extension\FormModel\Contract\FormErrorsContract;
use Yii\Extension\FormModel\Contract\FormModelContract;
use Yii\Extension\FormModel\Validator\Validator;
use Yiisoft\Strings\Inflector;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\Result;

use function array_key_exists;
use function array_keys;
use function explode;
use function is_subclass_of;
use function property_exists;
use function str_contains;
use function strrchr;
use function substr;

abstract class FormModel implements FormModelContract
{
    private array $attributes;
    private ?FormErrorsContract $formErrors = null;
    private ?Inflector $inflector = null;
    /** @psalm-var array<string, string|array> */
    private array $rawData = [];

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

    public function getHint(string $attribute): string
    {
        $hints = $this->getHints();
        $hint = $hints[$attribute] ?? '';
        $nestedHint = $this->getNestedValue('getHint', $attribute);

        $hint = match ($this->has($attribute)) {
            true => $nestedHint === '' ? $hint : $nestedHint,
            false => throw new InvalidArgumentException("Attribute '$attribute' does not exist."),
        };

        return $hint;
    }

    /**
     * @return string[]
     */
    public function getHints(): array
    {
        return [];
    }

    public function getLabel(string $attribute): string
    {
        $labels = $this->getLabels();
        $label = $labels[$attribute] ?? $this->generateLabel($attribute);
        $nestedLabel = $this->getNestedValue('getLabel', $attribute);

        $label = match ($this->has($attribute)) {
            true => $nestedLabel === '' ? $label : $nestedLabel,
            false => throw new InvalidArgumentException("Attribute '$attribute' does not exist."),
        };

        return $label;
    }

    /**
     * @return string[]
     */
    public function getLabels(): array
    {
        return [];
    }

    public function getPlaceholder(string $attribute): string
    {
        $placeHolders = $this->getPlaceholders();
        $placeHolder = $placeHolders[$attribute] ?? '';
        $nestedPlaceholder = $this->getNestedValue('getPlaceholder', $attribute);

        $placeHolder = match ($this->has($attribute)) {
            true => $nestedPlaceholder === '' ? $placeHolder : $nestedPlaceholder,
            false => throw new InvalidArgumentException("Attribute '$attribute' does not exist."),
        };

        return $placeHolder;
    }

    /**
     * @return string[]
     */
    public function getPlaceholders(): array
    {
        return [];
    }

    public function getRules(): array
    {
        return [];
    }

    public function has(string $attribute): bool
    {
        [$attribute, $nested] = $this->getNested($attribute);

        return $nested !== null || array_key_exists($attribute, $this->attributes);
    }

    /**
     * @param array $data
     * @param string|null $formName
     *
     * @return bool
     *
     * @psalm-param array<string, string|array> $data
     */
    public function load(array $data, ?string $formName = null): bool
    {
        $this->rawData = [];
        $scope = $formName ?? $this->getFormName();

        /** @psalm-var array<string, string> */
        $this->rawData = match (empty($scope)) {
            true => $data,
            false => isset($data[$scope]) ? $data[$scope] : [],
        };

        foreach ($this->rawData as $name => $value) {
            $this->set($name, $value);
        }

        return $this->rawData !== [];
    }

    public function set(string $name, mixed $value): void
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

    public function setFormErrors(FormErrorsContract $formErrors): void
    {
        $this->formErrors = $formErrors;
    }

    public function sets(array $data): void
    {
        /**
         * @var array<string, mixed> $data
         * @var mixed $value
         */
        foreach ($data as $name => $value) {
            $name = $this->getInflector()->toCamelCase($name);

            if ($this->has($name)) {
                $this->set($name, $value);
            } else {
                throw new InvalidArgumentException(sprintf('Attribute "%s" does not exist', $name));
            }
        }
    }

    public function validate(): bool
    {
        return (new Validator($this))->validate()->isValid();
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

    /**
     * Generates a user-friendly attribute label based on the give attribute name.
     *
     * This is done by replacing underscores, dashes and dots with blanks and changing the first letter of each word to
     * upper case.
     *
     * For example, 'department_name' or 'DepartmentName' will generate 'Department Name'.
     *
     * @param string $name the column name.
     *
     * @return string the attribute label.
     */
    private function generateLabel(string $name): string
    {
        return StringHelper::uppercaseFirstCharacterInEachWord(
            $this->getInflector()->toWords($name)
        );
    }

    private function getInflector(): Inflector
    {
        return match (empty($this->inflector)) {
            true => $this->inflector = new Inflector(),
            false => $this->inflector,
        };
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

    private function getNestedValue(string $method, string $attribute): string
    {
        $result = '';

        [$attribute, $nested] = $this->getNested($attribute);

        if ($nested !== null) {
            /** @var FormModelContract $attributeNestedValue */
            $attributeNestedValue = $this->getCastValue($attribute);
            /** @var string */
            $result = $attributeNestedValue->$method($nested);
        }

        return $result;
    }

    private function readProperty(string $attribute): mixed
    {
        $class = static::class;

        [$attribute, $nested] = $this->getNested($attribute);

        if (!property_exists($class, $attribute)) {
            throw new InvalidArgumentException("Undefined property: \"$class::$attribute\".");
        }

        /** @psalm-suppress MixedMethodCall */
        $getter = static function (FormModelContract $class, string $attribute, ?string $nested): mixed {
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
        $setter = static function (FormModelContract $class, string $attribute, mixed $value, ?string $nested): void {
            match ($nested) {
                null => $class->$attribute = $value,
                default => $class->$attribute->set($nested, $value),
            };
        };

        $setter = Closure::bind($setter, null, $this);

        /** @var Closure $setter */
        $setter($this, $attribute, $value, $nested);
    }
}
