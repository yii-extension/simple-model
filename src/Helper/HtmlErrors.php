<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Helper;

use Yii\Extension\Simple\Model\ModelInterface;

/**
 * HtmlErrors renders a list of errors for the specified model attribute.
 */
final class HtmlErrors
{
    /**
     * Returns the errors for all attributes.
     *
     * @param ModelInterface $model the form object.
     *
     * @return array the error messages.
     */
    public static function getAllErrors(ModelInterface $model): array
    {
        return $model->getFormErrors()->getAllErrors();
    }

    /**
     * Returns the errors for single attribute.
     *
     * @param ModelInterface $model the form object.
     * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
     */
    public static function getErrors(ModelInterface $model, string $attribute): array
    {
        return $model->getFormErrors()->getErrors($attribute);
    }

    /**
     * Returns the firts errors for all attributes as a one-dimensional array.
     *
     * @param ModelInterface $model the form object.
     *
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     */
    public static function getErrorSummaryFirstErrors(ModelInterface $model): array
    {
        return $model->getFormErrors()->getErrorSummaryFirstErrors();
    }

    /**
     * Returns the errors for all attributes as a one-dimensional array.
     *
     * @param ModelInterface $model the form object.
     *
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     */
    public static function getErrorSummary(ModelInterface $model): array
    {
        return $model->getFormErrors()->getErrorSummary();
    }

    /**
     * Return the attribute first error message.
     *
     * @param ModelInterface $model the form object.
     * @param string $attribute the attribute name or expression.
     *
     * @return string the error message. Empty string returned if there is no error.
     */
    public static function getFirstError(ModelInterface $model, string $attribute): string
    {
        return $model->getFormErrors()->getFirstError(HtmlModel::getAttributeName($model, $attribute));
    }

    /**
     * Returns a value indicating whether there is any validation error.
     *
     * @param ModelInterface $model the form object.
     * @param string|null $attribute attribute name. Use null to check all attributes.
     *
     * @return bool whether there is any error.
     */
    public static function hasErrors(ModelInterface $model, ?string $attribute = null): bool
    {
        return $model->getFormErrors()->hasErrors($attribute);
    }
}