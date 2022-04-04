<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel;

use Yiisoft\Validator\DataSet\AttributeDataSet;
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
    public function __construct(private FormModel|Model $formModel, private array $rowData)
    {
    }

    public function validate(): Result
    {
        $attributeDataSet = new AttributeDataSet($this->formModel, $this->rowData);
        $rules = array_merge((array) $attributeDataSet->getRules(), $this->formModel->getRules());
        $result = $this->validateRules($rules);
        $this->addFormErrors($result);
        return $result;
    }

    private function addFormErrors(Result $result): void
    {
        foreach ($result->getErrorMessagesIndexedByAttribute() as $attribute => $errors) {
            if ($this->formModel->has($attribute)) {
                foreach ($errors as $error) {
                    $this->formModel->error()->add($attribute, $error);
                }
            }
        }
    }

    private function validateRules(iterable $rules): Result
    {
        $context = new ValidationContext($this->formModel);
        $result = new Result();

        /** @psalm-var iterable<string, Rule[]> $rules */
        foreach ($rules as $attribute => $attributeRules) {
            $ruleSet = new RuleSet($attributeRules);
            $tempResult = $ruleSet->validate(
                $this->formModel->getAttributeValue($attribute),
                $context->withAttribute($attribute)
            );

            foreach ($tempResult->getErrors() as $error) {
                $result->addError($error->getMessage(), [$attribute, ...$error->getValuePath()]);
            }
        }

        return $result;
    }
}
