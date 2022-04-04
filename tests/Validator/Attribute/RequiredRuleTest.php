<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Attribute\FormErrorsAttributes;
use Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute\RequiredRule;

final class RequiredRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new RequiredRule();
        $this->assertFalse($formModel->validate());
        $this->assertSame(['required' => ['Value cannot be blank.']], FormErrorsAttributes::getAll($formModel));
    }
}
