<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use Yii\Extension\FormModel\Contract\FormErrorsContract;
use Yii\Extension\FormModel\Contract\FormModelContract;
use Yiisoft\Strings\Inflector;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RulesProviderInterface;

use function array_key_exists;
use function array_keys;
use function explode;
use function is_subclass_of;
use function property_exists;
use function str_contains;
use function strrchr;
use function substr;

abstract class FormModel implements FormModelContract, PostValidationHookInterface, RulesProviderInterface
{
    private array $attributes;
    private ?FormErrorsContract $formErrors = null;
    private ?Inflector $inflector = null;
    /** @psalm-var array<string, string|array> */
    private array $rawData = [];
    private bool $validated = false;

    public function __construct()
    {
        $this->attributes = $this->collectAttributes();
    }

    public function attributes(): array
    {
        return array_keys($this->attributes);
    }

    public function getHint(string $attribute): string
    {
        $attributeHints = $this->getHints();
        $hint = $attributeHints[$attribute] ?? '';
        $nestedAttributeHint = $this->getNestedValue('getHint', $attribute);
        return $nestedAttributeHint !== '' ? $nestedAttributeHint : $hint;
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

        $label = match ($this->has($attribute)) {
            true => $labels[$attribute] ?? $this->getNestedValue('getLabel', $attribute),
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
        $attributePlaceHolders = $this->getPlaceholders();
        $placeholder = $attributePlaceHolders[$attribute] ?? '';
        $nestedAttributePlaceholder = $this->getNestedValue('getPlaceholder', $attribute);
        return $nestedAttributePlaceholder !== '' ? $nestedAttributePlaceholder : $placeholder;
    }

    /**
     * @return string[]
     */
    public function getPlaceholders(): array
    {
        return [];
    }

    public function getCastValue(string $attribute): mixed
    {
        return $this->readProperty($attribute);
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->rawData[$attribute] ?? $this->getCastValue($attribute);
    }

    public function getFormErrors(): FormErrorsContract
    {
        return match (empty($this->formErrors)) {
            true => $this->formErrors = new FormErrors(),
            false => $this->formErrors,
        };
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

    public function has(string $attribute): bool
    {
        [$attribute, $nested] = $this->getNestedAttribute($attribute);

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

        if ($scope === '' && !empty($data)) {
            $this->rawData = $data;
        } elseif (isset($data[$scope])) {
            /** @var array<string, string> */
            $this->rawData = $data[$scope];
        }

        foreach ($this->rawData as $name => $value) {
            $this->set($name, $value);
        }

        return $this->rawData !== [];
    }

    public function set(string $name, mixed $value): void
    {
        [$realName] = $this->getNestedAttribute($name);

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

    public function processValidationResult(Result $result): void
    {
        foreach ($result->getErrorMessagesIndexedByAttribute() as $attribute => $errors) {
            if ($this->has($attribute)) {
                $this->addErrors([$attribute => $errors]);
            }
        }

        $this->validated = true;
    }

    public function getRules(): array
    {
        return [];
    }

    public function setFormErrors(FormErrorsContract $formErrors): void
    {
        $this->formErrors = $formErrors;
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
            if ($property->isStatic()) {
                continue;
            }

            /** @var ReflectionNamedType|null $type */
            $type = $property->getType();

            $attributes[$property->getName()] = $type !== null ? $type->getName() : '';
        }

        return $attributes;
    }

    /**
     * @psalm-param  non-empty-array<string, non-empty-list<string>> $items
     */
    private function addErrors(array $items): void
    {
        foreach ($items as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->getFormErrors()->add($attribute, $error);
            }
        }
    }

    private function getInflector(): Inflector
    {
        return match (empty($this->inflector)) {
            true => $this->inflector = new Inflector(),
            false => $this->inflector,
        };
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

    private function readProperty(string $attribute): mixed
    {
        $class = static::class;

        [$attribute, $nested] = $this->getNestedAttribute($attribute);

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
        [$attribute, $nested] = $this->getNestedAttribute($attribute);

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

    /**
     * @return string[]
     *
     * @psalm-return array{0: string, 1: null|string}
     */
    private function getNestedAttribute(string $attribute): array
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

        [$attribute, $nested] = $this->getNestedAttribute($attribute);

        if ($nested !== null) {
            /** @var FormModelContract $attributeNestedValue */
            $attributeNestedValue = $this->getCastValue($attribute);
            /** @var string */
            $result = $attributeNestedValue->$method($nested);
        }

        return $result;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }
}
