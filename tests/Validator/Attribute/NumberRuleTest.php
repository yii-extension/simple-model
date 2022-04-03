<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute\NumberRule;

final class NumberRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new NumberRule();
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(['number' => ['Value must be a number.']], FormErrorsAttributes::getAll($formModel));

        $this->assertTrue($formModel->load(['NumberRule' => ['number' => -1]]));
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(['number' => ['Value must be no less than 0.']], FormErrorsAttributes::getAll($formModel));

        $this->assertTrue($formModel->load(['NumberRule' => ['number' => 11]]));
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(
            ['number' => ['Value must be no greater than 10.']],
            FormErrorsAttributes::getAll($formModel)
        );
    }
}
