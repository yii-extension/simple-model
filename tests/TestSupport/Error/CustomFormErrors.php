<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\Error;

use Yii\Extension\Model\Contract\FormErrorsContract;

final class CustomFormErrors implements FormErrorsContract
{
    /** @psalm-param array<string, array<array-key, string>> $attributesErrors */
    public function __construct(private array $attributesErrors = [])
    {
    }

    public function add(string $attribute, string $error): void
    {
        $this->attributesErrors[$attribute][] = $error;
    }

    /** @psalm-param array<string, array<array-key, string>> $values */
    public function addMultiple(array $values): void
    {
        $this->attributesErrors = $values;
    }

    public function clear(?string $attribute = null): void
    {
        if ($attribute !== null) {
            unset($this->attributesErrors[$attribute]);
        } else {
            $this->attributesErrors = [];
        }
    }

    public function get(string $attribute): array
    {
        return $this->attributesErrors[$attribute] ?? [];
    }

    public function getAll(): array
    {
        return $this->attributesErrors;
    }

    public function getFirst(string $attribute): string
    {
        return match (empty($this->attributesErrors[$attribute])) {
            true => '',
            false => reset($this->attributesErrors[$attribute]),
        };
    }

    public function getFirsts(): array
    {
        if (empty($this->attributesErrors)) {
            return [];
        }

        $errors = [];

        foreach ($this->attributesErrors as $name => $es) {
            if (!empty($es)) {
                /** @var mixed */
                $errors[$name] = reset($es);
            }
        }

        return $errors;
    }

    public function getSummary(array $onlyAttributes = []): array
    {
        $errors = $this->attributesErrors;

        if ($onlyAttributes !== []) {
            $errors = array_intersect_key($errors, array_flip($onlyAttributes));
        }

        return $this->renderErrorSummary($errors);
    }

    public function getSummaryFirst(): array
    {
        return $this->renderErrorSummary([$this->getFirsts()]);
    }

    public function has(?string $attribute = null): bool
    {
        return match ($attribute) {
            null => !empty($this->attributesErrors),
            default => isset($this->attributesErrors[$attribute]),
        };
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
