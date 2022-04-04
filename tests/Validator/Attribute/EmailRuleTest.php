<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute\EmailRule;

final class EmailRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new EmailRule();
        $this->assertFalse($formModel->validate());
        $this->assertSame(
            ['email' => ['This value is not a valid email address.']],
            FormErrorsAttributes::getAll($formModel)
        );

        $this->assertTrue($formModel->load(['EmailRule' => ['email' => 'a@']]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(
            ['email' => ['This value is not a valid email address.']],
            FormErrorsAttributes::getAll($formModel)
        );
    }
}
