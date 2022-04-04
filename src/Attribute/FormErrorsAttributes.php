<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Attribute;

use Yii\Extension\Model\FormModel;

final class FormErrorsAttributes
{
    /**
     * Returns the errors for single attribute.
     *
     * @param FormModel $formModel the form object.
     * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
     */
    public static function get(FormModel $formModel, string $attribute): array
    {
        return $formModel->error()->get($attribute);
    }

    /**
     * Returns the errors for all attributes.
     *
     * @param FormModel $formModel the form object.
     *
     * @return array the error messages.
     */
    public static function getAll(FormModel $formModel): array
    {
        return $formModel->error()->getAll();
    }

    /**
     * Return the attribute first error message.
     *
     * @param FormModel $formModel the form object.
     * @param string $attribute the attribute name or expression.
     *
     * @return string the error message. Empty string returned if there is no error.
     */
    public static function getFirst(FormModel $formModel, string $attribute): string
    {
        return $formModel->error()->getFirst(FormModelAttributes::getName($formModel, $attribute));
    }

    /**
     * Returns the first error of every attribute in the model.
     *
     * @param FormModel $formModel the form object.
     *
     * @return array The first error message for each attribute in a model. Empty array is returned if there is no
     * error.
     */
    public static function getFirsts(FormModel $formModel): array
    {
        return $formModel->error()->getFirsts();
    }

    /**
     * Returns the errors for all attributes as a one-dimensional array.
     *
     * @param FormModel $formModel the form object.
     * @param array $onlyAttributes list of attributes whose errors should be returned.
     *
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     */
    public static function getSummary(FormModel $formModel, array $onlyAttributes = []): array
    {
        return $formModel->error()->getSummary($onlyAttributes);
    }

    /**
     * Returns first errors for all attributes as a one-dimensional array.
     *
     * @param FormModel $formModel the form object.
     *
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     */
    public static function getSummaryFirst(FormModel $formModel): array
    {
        return $formModel->error()->getSummaryFirst();
    }

    /**
     * Returns a value indicating whether there is any validation error.
     *
     * @param FormModel $formModel the form object.
     * @param string|null $attribute attribute name. Use null to check all attributes.
     *
     * @return bool whether there is any error.
     */
    public static function has(FormModel $formModel, ?string $attribute = null): bool
    {
        return $formModel->error()->has($attribute);
    }
}
