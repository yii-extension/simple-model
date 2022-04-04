<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Attribute\FormErrorsAttributes;
use Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute\UrlRule;

final class UrlRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new UrlRule();
        $this->assertFalse($formModel->validate());
        $this->assertSame(['url' => ['This value is not a valid URL.']], FormErrorsAttributes::getAll($formModel));
    }
}
