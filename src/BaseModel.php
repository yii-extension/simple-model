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
    /** @psalm-var array<string, array<array-key, string>> */
    private array $attributesErrors = [];
    private string $formErrorsClass = ModelErrors::class;
    private ModelErrorsInterface $formErrors;
    private Inflector $inflector;
    private bool $validated = false;

    public function __construct()
    {
        $this->attributes = $this->collectAttributes();
        $this->inflector = new Inflector();
        $this->formErrors = $this->createFormErrors();
    }

    public function getAttributeHint(string $attribute): string
    {
        $attributeHints = $this->getAttributeHints();
        $hint = $attributeHints[$attribute] ?? '';
        $nestedAttributeHint = $this->getNestedAttributeValue('getAttributeHint', $attribute);

        return $nestedAttributeHint !== '' ? $nestedAttributeHint : $hint;
    }

    /**
     * @return string[]
     */
    public function getAttributeHints(): array
    {
        return [];
    }

    public function getAttributeLabel(string $attribute): string
    {
        $label = $this->generateAttributeLabel($attribute);
        $labels = $this->getAttributeLabels();

        if (array_key_exists($attribute, $labels)) {
            $label = $labels[$attribute];
        }

        $nestedAttributeLabel = $this->getNestedAttributeValue('getAttributeLabel', $attribute);

        return $nestedAttributeLabel !== '' ? $nestedAttributeLabel : $label;
    }

    /**
     * @return string[]
     */
    public function getAttributeLabels(): array
    {
        return [];
    }

    public function getAttributePlaceholder(string $attribute): string
    {
        $attributePlaceHolders = $this->getAttributePlaceholders();
        $placeholder = $attributePlaceHolders[$attribute] ?? '';
        $nestedAttributePlaceholder = $this->getNestedAttributeValue('getAttributePlaceholder', $attribute);

        return $nestedAttributePlaceholder !== '' ? $nestedAttributePlaceholder : $placeholder;
    }

    /**
     * @return string[]
     */
    public function getAttributePlaceholders(): array
    {
        return [];
    }

    /**
     * @return iterable|object|scalar|Stringable|null
     */
    public function getAttributeValue(string $attribute)
    {
        return $this->readProperty($attribute);
    }

    /**
     * @return ModelErrorsInterface Get FormErrors object.
     */
    public function getFormErrors(): ModelErrorsInterface
    {
        return $this->formErrors;
    }

    /**
     * @return string Returns classname without a namespace part or empty string when class is anonymous
     */
    public function getFormName(): string
    {
        if (strpos(static::class, '@anonymous') !== false) {
            return '';
        }

        $className = strrchr(static::class, '\\');
        if ($className === false) {
            return static::class;
        }

        return substr($className, 1);
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes);
    }

    /**
     * @return string[]
     */
    public function getError(string $attribute): array
    {
        return $this->attributesErrors[$attribute] ?? [];
    }

    /**
     * @param array $data
     * @param string|null $formName
     *
     * @return bool
     */
    public function load(array $data, ?string $formName = null): bool
    {
        $scope = $formName ?? $this->getFormName();

        /**
         * @psalm-var array<string, scalar|Stringable|null>
         */
        $values = [];

        if ($scope === '' && !empty($data)) {
            $values = $data;
        } elseif (isset($data[$scope])) {
            /** @var mixed */
            $values = $data[$scope];
        }

        /** @var array<string, scalar|Stringable|null> $values */
        foreach ($values as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $values !== [];
    }

    /**
     * @param iterable|object|scalar|Stringable|null $value
     *
     * @psalm-suppress PossiblyInvalidCast
     */
    public function setAttribute(string $name, $value): void
    {
        [$realName] = $this->getNestedAttribute($name);

        if (isset($this->attributes[$realName])) {
            switch ($this->attributes[$realName]) {
                case 'bool':
                    $this->writeProperty($name, (bool) $value);
                    break;
                case 'float':
                    $this->writeProperty($name, (float) $value);
                    break;
                case 'int':
                    $this->writeProperty($name, (int) $value);
                    break;
                case 'string':
                    $this->writeProperty($name, (string) $value);
                    break;
                default:
                    $this->writeProperty($name, $value);
                    break;
            }
        }
    }

    public function setAttributes(array $data): void
    {
        /** @var array<string, null|scalar|object|Stringable> $data */
        foreach ($data as $name => $value) {
            $name = $this->inflector->toCamelCase($name);

            if ($this->hasAttribute($name)) {
                $this->setAttribute($name, $value);
            } else {
                throw new InvalidArgumentException(sprintf('Attribute "%s" does not exist', $name));
            }
        }
    }

    public function setFormErrorsClass(string $formErrorsClass): void
    {
        $this->formErrorsClass = $formErrorsClass;
    }

    public function processValidationResult(ResultSet $resultSet): void
    {
        $this->formErrors->clear();
        /** @var array<array-key, Resultset> $resultSet */
        foreach ($resultSet as $attribute => $result) {
            if ($result->isValid() === false) {
                /** @psalm-suppress InvalidArgument */
                $this->addErrors([$attribute => $result->getErrors()]);
            }
        }
        $this->validated = true;
    }

    public function getRules(): array
    {
        return [];
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
     * @psalm-param array<string, array<array-key, string>> $items
     */
    private function addErrors(array $items): void
    {
        $this->formErrors->addErrors($items);
    }

    private function clearErrors(): void
    {
        $this->attributesErrors = [];
        $this->validated = false;
    }

    private function createFormErrors(): ModelErrorsInterface
    {
        $formErrorsClass = $this->formErrorsClass;
        $formErrorsClass = new $formErrorsClass();

        if (!$formErrorsClass instanceof ModelErrorsInterface) {
            throw new InvalidArgumentException('Form errors class must implement ' . ModelErrorsInterface::class);
        }

        return $formErrorsClass;
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
        return StringHelper::uppercaseFirstCharacterInEachWord(
            $this->inflector->toWords($name)
        );
    }

    /**
     * @return iterable|scalar|Stringable|null
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MissingClosureReturnType
     */
    private function readProperty(string $attribute)
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
     * @param iterable|object|scalar|Stringable|null $value
     *
     * @psalm-suppress MissingClosureReturnType
     */
    private function writeProperty(string $attribute, $value): void
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

    /**
     * @return string[]
     *
     * @psalm-return array{0: string, 1: null|string}
     */
    private function getNestedAttribute(string $attribute): array
    {
        if (strpos($attribute, '.') === false) {
            return [$attribute, null];
        }

        [$attribute, $nested] = explode('.', $attribute, 2);

        /** @var object|string */
        $attributeNested = $this->attributes[$attribute] ?? '';

        if (!is_subclass_of($attributeNested, self::class)) {
            throw new InvalidArgumentException("Attribute \"$attribute\" is not a nested attribute.");
        }

        return [$attribute, $nested];
    }

    private function getNestedAttributeValue(string $method, string $attribute): string
    {
        $result = '';

        [$attribute, $nested] = $this->getNestedAttribute($attribute);

        if ($nested !== null) {
            /** @var ModelInterface $attributeNestedValue */
            $attributeNestedValue = $this->getAttributeValue($attribute);
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
