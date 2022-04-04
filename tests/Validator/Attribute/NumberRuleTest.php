<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Attribute\FormErrorsAttributes;
use Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute\NumberRule;

final class NumberRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new NumberRule();
        $this->assertFalse($formModel->validate());
        $this->assertSame(['number' => ['Value must be a number.']], FormErrorsAttributes::getAll($formModel));

        $this->assertTrue($formModel->load(['NumberRule' => ['number' => -1]]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(['number' => ['Value must be no less than 0.']], FormErrorsAttributes::getAll($formModel));

        $this->assertTrue($formModel->load(['NumberRule' => ['number' => 11]]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(
            ['number' => ['Value must be no greater than 10.']],
            FormErrorsAttributes::getAll($formModel)
        );
    }
}
