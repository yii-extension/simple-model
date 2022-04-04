<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute\RegexRule;

final class RegexRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new RegexRule();
        $this->assertTrue($formModel->load(['RegexRule' => ['regex' => '??']]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(['regex' => ['Value is invalid.']], FormErrorsAttributes::getAll($formModel));
    }
}
