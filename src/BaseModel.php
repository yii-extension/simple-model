<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use Stringable;
use Yiisoft\Strings\Inflector;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\ResultSet;

use function array_key_exists;
use function array_merge;
use function explode;
use function get_object_vars;
use function is_subclass_of;
use function reset;
use function sprintf;
use function strpos;

/**
 * Form model represents an HTML form: its data, validation and presentation.
 */
abstract class BaseModel implements ModelInterface
{
    private array $attributes;
    /** @var array<string, array<array-key, string>> */
    private array $attributesErrors = [];
    private Inflector $inflector;

    public function __construct()
    {
        $this->inflector = new Inflector();
        $this->attributes = $this->getAttributes();
    }

    public function addError(string $attribute, string $error): void
    {
        $this->addErrors([$attribute => [$error]]);
    }

    public function getAttributeHint(string $attribute): string
    {
        $attributeHints = $this->getAttributeHints();

        /** @var string */
        $hint = $attributeHints[$attribute] ?? '';

        [$attribute, $nested] = $this->getNestedAttribute($attribute);

        if ($nested !== null) {
            /** @var ModelInterface */
            $attributeNestedValue = $this->getAttributeValue($attribute);
            $hint = $attributeNestedValue->getAttributeHint($nested);
        }

        return $hint;
    }

    public function getAttributeHints(): array
    {
        return [];
    }

    public function getAttributeLabel(string $attribute): string
    {
        $label = $this->generateAttributeLabel($attribute);

        if (array_key_exists($attribute, $this->getAttributeLabels())) {
            /** @var string */
            $label = $this->getAttributeLabels()[$attribute];
        }

        [$attribute, $nested] = $this->getNestedAttribute($attribute);

        if ($nested !== null) {
            /** @var ModelInterface */
            $attributeNestedValue = $this->getAttributeValue($attribute);
            $label = $attributeNestedValue->getAttributeLabel($nested);
        }

        return $label;
    }

    public function getAttributeLabels(): array
    {
        return [];
    }

    /**
     * @return null|scalar|Stringable|iterable
     */
    public function getAttributeValue(string $attribute)
    {
        return $this->readAttribute($attribute);
    }

    public function getFormName(): string
    {
        return substr(strrchr(static::class, '\\'), 1);
    }

    public function getError(string $attribute): array
    {
        return isset($this->attributesErrors[$attribute]) ? $this->attributesErrors[$attribute] : [];
    }

    public function getErrors(): array
    {
        return $this->attributesErrors;
    }

    public function getErrorSummary(bool $showAllErrors = true): array
    {
        $lines = [];
        $errors = $showAllErrors ? $this->getErrors() : [$this->getFirstErrors()];

        /** @var array $error */
        foreach ($errors as $error) {
            $lines = array_merge($lines, $error);
        }

        return $lines;
    }

    public function getFirstError(string $attribute): string
    {
        if (empty($this->attributesErrors[$attribute])) {
            return '';
        }

        return reset($this->attributesErrors[$attribute]);
    }

    public function getFirstErrors(): array
    {
        if (empty($this->attributesErrors)) {
            return [];
        }

        $errors = [];

        foreach ($this->attributesErrors as $name => $es) {
            if (!empty($es)) {
                $errors[$name] = reset($es);
            }
        }

        return $errors;
    }

    public function getRules(): array
    {
        return [];
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes);
    }

    public function hasErrors(?string $attribute = null): bool
    {
        return $attribute === null ? !empty($this->attributesErrors) : isset($this->attributesErrors[$attribute]);
    }

    public function load(array $data): bool
    {
        $scope = $this->getFormName();

        $values = [];

        if (isset($data[$scope])) {
            /** @var mixed */
            $values = $data[$scope];
        }

        /** @var array<string, null|scalar|Stringable> $values */
        foreach ($values as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $values !== [];
    }

    public function processValidationResult(ResultSet $resultSet): void
    {
        $this->clearErrors();

        /** @var array<array-key, Resultset> $resultSet */
        foreach ($resultSet as $attribute => $result) {
            if ($result->isValid() === false) {
                $this->addErrors([$attribute => $result->getErrors()]);
            }
        }
    }

    /**
     * @param null|scalar|Stringable|iterable $value
     */
    public function setAttribute(string $name, $value): void
    {
        [$realName] = $this->getNestedAttribute($name);

        if (isset($this->attributes[$realName])) {
            switch ($this->attributes[$realName]) {
                case 'bool':
                    $value = (bool) $value;
                    break;
                case 'float':
                    $value = (float) $value;
                    break;
                case 'int':
                    $value = (int) $value;
                    break;
            }
            $this->writeAttribute($name, $value);
        }
    }

    private function addErrors(array $items): void
    {
        /**
         * @var array<string, array<array-key, string>> $items
         */
        foreach ($items as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->attributesErrors[$attribute][] = $error;
            }
        }
    }

    private function clearErrors(string $attribute = ''): void
    {
        if ($attribute === '') {
            $this->attributesErrors = [];
        } else {
            unset($this->attributesErrors[$attribute]);
        }
    }

    /**
     * Generates a user friendly attribute label based on the give attribute name.
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
    private function generateAttributeLabel(string $name): string
    {
        return StringHelper::uppercaseFirstCharacterInEachWord($this->inflector->toWords($name));
    }

    /**
     * Returns the list of attribute types indexed by attribute names.
     *
     * By default, this method returns all non-static properties of the class.
     *
     * @return array list of attribute types indexed by attribute names.
     */
    private function getAttributes(): array
    {
        $class = new ReflectionClass($this);
        $attributes = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            /** @var ReflectionNamedType|null $type */
            $type = $property->getType();
            if ($type === null) {
                throw new InvalidArgumentException(
                    sprintf(
                        'You must specify the type hint for "%s" property in "%s" class.',
                        $property->getName(),
                        $property->getDeclaringClass()->getName(),
                    )
                );
            }

            $attributes[$property->getName()] = $type->getName();
        }

        return $attributes;
    }

    /**
     * @return (null|string)[]
     *
     * @psalm-return array{0: string, 1: null|string}
     */
    private function getNestedAttribute(string $attribute): array
    {
        if (strpos($attribute, '.') === false) {
            return [$attribute, null];
        }

        [$attribute, $nested] = explode('.', $attribute);

        /** @var object */
        $attributeNested = $this->attributes[$attribute];

        if (!is_subclass_of($attributeNested, ModelInterface::class)) {
            throw new InvalidArgumentException('Nested attribute can only be of ' . ModelInterface::class . ' type.');
        }

        return [$attribute, $nested];
    }

    /**
     * @return null|scalar|Stringable|iterable
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MissingClosureReturnType
     */
    private function readAttribute(string $attribute)
    {
        $class = static::class;

        [$attribute, $nested] = $this->getNestedAttribute($attribute);

        if (!property_exists($class, $attribute)) {
            throw new InvalidArgumentException("Undefined property: \"$class::$attribute\".");
        }

        /** @psalm-suppress MixedMethodCall */
        $getter = static fn (ModelInterface $class, string $attribute) => $nested === null
            ? $class->$attribute
            : $class->$attribute->getAttributeValue($nested);

        $getter = Closure::bind($getter, null, $this);

        /** @var Closure $getter */
        return $getter($this, $attribute);
    }

    /**
     * @param string $attribute
     * @param null|scalar|Stringable|iterable $value
     *
     * @psalm-suppress MissingClosureReturnType
     */
    private function writeAttribute(string $attribute, $value): void
    {
        [$attribute, $nested] = $this->getNestedAttribute($attribute);

        /**
         * @psalm-suppress MissingClosureParamType
         * @psalm-suppress MixedMethodCall
         */
        $setter = static fn (ModelInterface $class, string $attribute, $value) => $nested === null
            ? $class->$attribute = $value
            : $class->$attribute->setAttribute($nested, $value);

        $setter = Closure::bind($setter, null, $this);

        /** @var Closure $setter */
        $setter($this, $attribute, $value);
    }
}
