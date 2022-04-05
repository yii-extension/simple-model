<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Contract;

interface ModelErrorsContract
{
    /**
     * Add an error for the specified attribute.
     *
     * @param string $attribute Attribute name.
     * @param string $error Attribute error message.
     */
    public function add(string $attribute, string $error): void;

    /**
     * Add errors for the model instance.
     *
     * @psalm-param array<string, array<array-key, string>> $values
     */
    public function addErrors(array $values): void;

    /**
     * Removes error for attributes.
     *
     * @param string|null $attribute Attribute name.
     */
    public function clear(?string $attribute = null): void;

    /**
     * Returns errors for an attribute with a given name.
     *
     * @param string $attribute Attribute name.
     *
     * @return array
     *
     * @psalm-return string[]
     */
    public function get(string $attribute): array;

    /**
     * Returns errors for all attributes.
     *
     * @return array Errors for all attributes.
     *
     * Note that when returning errors for all attributes, the result is a two-dimensional array, like the following:
     *
     * ```php
     * [
     *     'username' => [
     *         'Username is required.',
     *         'Username must contain only word characters.',
     *     ],
     *     'email' => [
     *         'Email address is invalid.',
     *     ]
     * ]
     * ```
     *
     * {@see getFirst()}
     * {@see getFirsts()}
     *
     * @psalm-return array<string, array<string>>
     */
    public function getAll(): array;

    /**
     * Returns the first error of the specified attribute.
     *
     * @param string $attribute attribute name.
     *
     * @return string the error message. Empty string is returned if there is no error.
     *
     * {@see get()}
     * {@see getFirsts()}
     */
    public function getFirst(string $attribute): string;

    /**
     * Returns the first error of every attribute in the model.
     *
     * @return array the first errors. The array keys are the attribute names, and the array values are the
     * corresponding error messages. An empty array will be returned if there is no error.
     *
     * {@see get()}
     * {@see getFirst()}
     */
    public function getFirsts(): array;

    /**
     * Returns errors for all attributes as a one-dimensional array.
     *
     * @param array $onlyAttributes List of attributes to return errors.
     *
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     *
     * {@see get()}
     * {@see getFirsts(){}
     */
    public function getSummary(array $onlyAttributes = []): array;

    /**
     * Returns the first error of every attribute in the collection.
     *
     * @return array the first error of every attribute in the collection. Empty array is returned if no error.
     */
    public function getSummaryFirst(): array;

    /**
     * Returns a value indicating whether there is any validation error.
     *
     * @param string|null $attribute attribute name. Use null to check all attributes.
     *
     * @return bool whether there is any error.
     */
    public function has(?string $attribute = null): bool;
}
