<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Contract;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\RulesProviderInterface;

interface ModelContract extends DataSetInterface, RulesProviderInterface
{
    /**
     * @return FormErrorsContract Validation errors.
     */
    public function error(): FormErrorsContract;

    /**
     * Returns the value (raw data) for the specified attribute.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    public function getCastValue(string $attribute): mixed;

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by {@see \Yii\Extension\Model\FormModelAttributes} to determine how to name the
     * input fields for the attributes in a model.
     * If the form name is "A" and an attribute name is "b", then the corresponding input name would be "A[b]".
     * If the form name is an empty string, then the input name would be "b".
     *
     * The purpose of the above naming schema is that for forms which contain multiple different models, the attributes
     * of each model are grouped in sub-arrays of the POST-data, and it is easier to differentiate between them.
     *
     * By default, this method returns the model class name (without the namespace part) as the form name. You may
     * override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     *
     * {@see load()}
     */
    public function getFormName(): string;

    /**
     * Return rules using `PHP` attributes.
     */
    public function getRulesWithAttributes(): iterable;

    /**
     * If there is such attribute in the set.
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function has(string $attribute): bool;

    /**
     * Return whether the form model is empty.
     */
    public function isEmpty(): bool;

    /**
     * Populates the model with input data.
     *
     * which, with `load()` can be written as:
     *
     * ```php
     * $body = $request->getParsedBody();
     * $method = $request->getMethod();
     *
     * if ($method === Method::POST && $loginForm->load($body)) {
     *     // handle success
     * }
     * ```
     *
     * `load()` gets the `'FormName'` from the {@see getFormName()} method (which you may override), unless the
     * `$formName` parameter is given. If the form name is empty string, `load()` populates the model with the whole of
     * `$data` instead of `$data['FormName']`.
     *
     * @param array $data the data array to load, typically server request attributes.
     * @param string|null $formName scope from which to get data
     *
     * @return bool whether `load()` found the expected form in `$data`.
     *
     * @psalm-param array<string, string> $data
     */
    public function load(array $data, ?string $formName = null): bool;

    /**
     * Set specified attribute
     *
     * @param string $name of the attribute to set
     * @param mixed $value
     */
    public function setValue(string $name, mixed $value): void;

    /**
     * Set values for attributes.
     *
     * @param array $data the key-value pairs to set for the attributes.
     */
    public function setValues(array $data): void;

    /**
     * Set custom form errors instance.
     */
    public function setFormErrors(FormErrorsContract $formErrors): void;

    /**
     * Validate the FormModel instance.
     */
    public function validate(): bool;
}
