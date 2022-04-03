<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute\RequiredRule;

final class RequiredRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new RequiredRule();
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(['required' => ['Value cannot be blank.']], FormErrorsAttributes::getAll($formModel));
    }
}
