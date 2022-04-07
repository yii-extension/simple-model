<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Attribute\FormErrorsAttributes;
use Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute\HasLengthRule;

final class HasLengthRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new HasLengthRule();
        $this->assertFalse($formModel->validate());
        $this->assertSame(
            [
                'hasLengthRule' => [
                    'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                ],
            ],
            FormErrorsAttributes::getAll($formModel)
        );

        $this->assertTrue($formModel->load(['HasLengthRule' => ['hasLengthRule' => 'al']]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(
            [
                'hasLengthRule' => [
                    'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                ],
            ],
            FormErrorsAttributes::getAll($formModel)
        );

        $this->assertTrue($formModel->load(['HasLengthRule' => ['hasLengthRule' => 'samdark']]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(
            [
                'hasLengthRule' => [
                    'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                ],
            ],
            FormErrorsAttributes::getAll($formModel)
        );
    }
}
