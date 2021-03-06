<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\Validator\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Attribute\FormErrorsAttributes;
use Yii\Extension\Model\Tests\TestSupport\FormModel\Attribute\IpRule;

final class IpRuleTest extends TestCase
{
    public function testAttribute(): void
    {
        $formModel = new IpRule();
        $this->assertTrue($formModel->load(['IpRule' => ['ip' => '192.168']]));
        $this->assertFalse($formModel->validate());
        $this->assertSame(['ip' => ['Must be a valid IP address.']], FormErrorsAttributes::getAll($formModel));
    }
}
