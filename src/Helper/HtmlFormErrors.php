<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Helper;

use Yii\Extension\Simple\Model\FormModelInterface;

/**
 * HtmlFormErrors renders a list of errors for the specified model attribute.
 */
final class HtmlFormErrors
{
    /**
     * Returns the errors for all attributes.
     *
     * @param FormModelInterface $formModel the form object.
     *
     * @return array the all errors message.
     */
    public static function getAllErrors(FormModelInterface $formModel): array
    {
        return $formModel->getFormErrors()->getAllErrors();
    }

    /**
     * Returns the errors for single attribute.
     *
     * @param FormModelInterface $formModel the form object.
     * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
     *
     * @return array the attribute error message.
     */
    public static function getErrors(FormModelInterface $formModel, string $attribute): array
    {
        return $formModel->getFormErrors()->getErrors($attribute);
    }

    /**
     * Returns the first errors for all attributes as a one-dimensional array.
     *
     * @param FormModelInterface $formModel the form object.
     *
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     */
    public static function getErrorSummaryFirstErrors(FormModelInterface $formModel): array
    {
        return $formModel->getFormErrors()->getErrorSummaryFirstErrors();
    }

    /**
     * Returns the errors for all attributes as a one-dimensional array.
     *
     * @param FormModelInterface $formModel the form object.
     *
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     */
    public static function getErrorSummary(FormModelInterface $formModel): array
    {
        return $formModel->getFormErrors()->getErrorSummary();
    }

    /**
     * Return the attribute first error message.
     *
     * @param FormModelInterface $formModel the form object.
     * @param string $attribute the attribute name or expression.
     *
     * @return string the error message. Empty string returned if there is no error.
     */
    public static function getFirstError(FormModelInterface $formModel, string $attribute): string
    {
        return $formModel->getFormErrors()->getFirstError(HtmlForm::getAttributeName($formModel, $attribute));
    }

    /**
     * Returns the first error of every attribute in the model.
     *
     * @param FormModelInterface $formModel the form object.
     *
     * @return array the error message for all attributes. Empty array is returned if no error.
     */
    public static function getFirstErrors(FormModelInterface $formModel): array
    {
        return $formModel->getFormErrors()->getFirstErrors();
    }

    /**
     * Returns a value indicating whether there is any validation error.
     *
     * @param FormModelInterface $formModel the form object.
     * @param string|null $attribute attribute name. Use null to check all attributes.
     *
     * @return bool whether there is any error.
     */
    public static function hasErrors(FormModelInterface $formModel, ?string $attribute = null): bool
    {
        return $formModel->getFormErrors()->hasErrors($attribute);
    }
}
