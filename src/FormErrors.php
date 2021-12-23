<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model;

/**
 * FormErrors represents a form validation errors collection.
 */
final class FormErrors implements FormErrorsInterface
{
    /** @psalm-var array<string, array<array-key, string>> */
    private array $attributesErrors;

    public function __construct(array $attributesErrors = [])
    {
        /** @psalm-var array<string, array<array-key, string>> */
        $this->attributesErrors = $attributesErrors;
    }

    public function addError(string $attribute, string $error): void
    {
        $this->attributesErrors[$attribute][] = $error;
    }

    public function addErrors(array $items): void
    {
        foreach ($items as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->addError($attribute, $error);
            }
        }
    }

    public function clear(string $attribute = null): void
    {
        if ($attribute !== null) {
            unset($this->attributesErrors[$attribute]);
        } else {
            $this->attributesErrors = [];
        }
    }

    public function getAllErrors(): array
    {
        return $this->attributesErrors;
    }

    public function getErrors(string $attribute): array
    {
        return $this->attributesErrors[$attribute] ?? [];
    }

    public function getErrorSummary(): array
    {
        return $this->renderErrorSummary($this->getAllErrors());
    }

    public function getErrorSummaryFirstErrors(): array
    {
        return $this->renderErrorSummary([$this->getFirstErrors()]);
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

    public function hasErrors(?string $attribute = null): bool
    {
        return $attribute === null ? !empty($this->attributesErrors) : isset($this->attributesErrors[$attribute]);
    }

    private function renderErrorSummary(array $errors): array
    {
        $lines = [];

        /** @var string[] errors */
        foreach ($errors as $error) {
            $lines = array_merge($lines, $error);
        }

        return $lines;
    }
}
