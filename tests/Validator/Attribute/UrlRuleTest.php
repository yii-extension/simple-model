<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Attribute\UrlRule;

final class UrlRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new UrlRule();
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(['url' => ['This value is not a valid URL.']], FormErrorsAttributes::getAll($formModel));
    }
}
