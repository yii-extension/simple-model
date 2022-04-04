<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Attribute\FormErrorsAttributes;
use Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute\RegexRule;

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
