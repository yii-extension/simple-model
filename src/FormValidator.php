<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel;

use Yii\Extension\FormModel\Contract\FormModelContract;
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_object;

/**
 * Validator validates {@link FormModelContract} against rules set for data set attributes.
 */
final class FormValidator
{
    public function __construct(private FormModelContract $formModel)
    {
    }

    public function validate(): Result
    {
        $context = new ValidationContext($this->formModel);
        $result = new Result();
        $rules = $this->formModel->getRules();

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

        foreach ($result->getErrorMessagesIndexedByAttribute() as $attribute => $errors) {
            if ($this->formModel->has($attribute)) {
                $this->addError([$attribute => $errors]);
            }
        }

        return $result;
    }

    public function validateWithAttributes(array $rawData): Result
    {
        $attributeDataSet = new AttributeDataSet($this->formModel, $rawData);
        $context = new ValidationContext($this->formModel);
        $result = new Result();
        $rules = $attributeDataSet->getRules();

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

        foreach ($result->getErrorMessagesIndexedByAttribute() as $attribute => $errors) {
            if ($this->formModel->has($attribute)) {
                $this->addError([$attribute => $errors]);
            }
        }

        return $result;
    }

    /**
     * @psalm-param array<string, list<string>> $items
     */
    private function addError(array $items): void
    {
        foreach ($items as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->formModel->error()->add($attribute, $error);
            }
        }
    }
}
