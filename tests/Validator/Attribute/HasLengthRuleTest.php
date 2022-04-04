<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute\HasLengthRule;

final class HasLengthRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new HasLengthRule();
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(
            [
                'hasLengthRule' => [
                    'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.'
                ],
            ],
            FormErrorsAttributes::getAll($formModel)
        );

        $this->assertTrue($formModel->load(['HasLengthRule' => ['hasLengthRule' => 'al']]));
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(
            [
                'hasLengthRule' => [
                    'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.'
                ],
            ],
            FormErrorsAttributes::getAll($formModel)
        );

        $this->assertTrue($formModel->load(['HasLengthRule' => ['hasLengthRule' => 'samdark']]));
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(
            [
                'hasLengthRule' => [
                    'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.'
                ],
            ],
            FormErrorsAttributes::getAll($formModel)
        );
    }
}