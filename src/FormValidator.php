<?php

declare(strict_types=1);

namespace Yii\Extension\Model;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_object;

/**
 * Validator validates {@link FormModel} against rules set for data set attributes.
 */
final class FormValidator
{
    public function __construct(private FormModel|Model $model, private array $rowData)
    {
    }

    public function validate(): Result
    {
        $rules = array_merge($this->model->getRulesWithAttributes(), $this->model->getRules());
        $result = $this->validateRules($rules);
        $this->addFormErrors($result);
        return $result;
    }

    private function addFormErrors(Result $result): void
    {
        foreach ($result->getErrorMessagesIndexedByAttribute() as $attribute => $errors) {
            if ($this->model->has($attribute)) {
                foreach ($errors as $error) {
                    $this->model->error()->add($attribute, $error);
                }
            }
        }
    }

    private function validateRules(iterable $rules): Result
    {
        $context = new ValidationContext($this->model);
        $result = new Result();

        /** @psalm-var iterable<string, Rule[]> $rules */
        foreach ($rules as $attribute => $attributeRules) {
            $ruleSet = new RuleSet($attributeRules);
            $tempResult = $ruleSet->validate(
                $this->model->getAttributeValue($attribute),
                $context->withAttribute($attribute)
            );

            foreach ($tempResult->getErrors() as $error) {
                $result->addError($error->getMessage(), [$attribute, ...$error->getValuePath()]);
            }
        }

        return $result;
    }
}
