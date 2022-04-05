<?php

declare(strict_types=1);

namespace Yii\Extension\Model;

use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ScalarDataSet;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

use function is_array;
use function is_object;

/**
 * Validator validates {@link FormModel} against rules set for data set attributes.
 */
final class FormValidator implements ValidatorInterface
{
    public function __construct()
    {
    }

    public function validate($data, iterable $rules = []): Result
    {
        $data = $this->normalizeDataSet($data);

        if ($data instanceof RulesProviderInterface) {
            $rules = array_merge((array) $data->getRules(), (array) $rules);
        }

        $context = new ValidationContext($data);
        $result = new Result();

        /** @psalm-var iterable<string, Rule[]> $rules */
        foreach ($rules as $attribute => $attributeRules) {
            $ruleSet = new RuleSet($attributeRules);
            $tempResult = $ruleSet->validate(
                $data->getAttributeValue($attribute),
                $context->withAttribute($attribute)
            );

            foreach ($tempResult->getErrors() as $error) {
                $result->addError($error->getMessage(), [$attribute, ...$error->getValuePath()]);
            }
        }

        return $result;
    }

    private function normalizeDataSet(mixed $data): DataSetInterface
    {
        if ($data instanceof DataSetInterface) {
            return $data;
        }

        if (is_object($data) || is_array($data)) {
            return new ArrayDataSet((array) $data);
        }

        return new ScalarDataSet($data);
    }
}
