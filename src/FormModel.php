<?php

declare(strict_types=1);

namespace Yii\Extension\Model;

use InvalidArgumentException;
use Yii\Extension\Model\Contract\FormModelContract;
use Yiisoft\Strings\StringHelper;

abstract class FormModel extends Model implements FormModelContract
{
    public function getHint(string $attribute): string
    {
        $hints = $this->getHints();
        $hint = $hints[$attribute] ?? '';
        $nestedHint = $this->getNestedValue('getHint', $attribute);

        return match ($this->has($attribute)) {
            true => $nestedHint === '' ? $hint : $nestedHint,
            false => throw new InvalidArgumentException("Attribute '$attribute' does not exist."),
        };
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
}
