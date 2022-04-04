<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute\InRangeRule;

final class InRangeRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new InRangeRule();
        $this->assertFalse($formModel->validate());
        $this->assertSame(['inRange' => ['This value is invalid.']], FormErrorsAttributes::getAll($formModel));

        $this->assertTrue($formModel->load(['InRangeRule' => ['inRange' => '11']]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(['inRange' => ['This value is invalid.']], FormErrorsAttributes::getAll($formModel));
    }
}
