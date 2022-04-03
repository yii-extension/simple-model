<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\ValidatorAttribute;

final class ValidatorAttributeTest extends TestCase
{
    public function testRequired(): void
    {
        $formModel = new ValidatorAttribute();
        $this->assertFalse($formModel->validateWithAttributes());
        $this->assertSame(['required' => ['Value cannot be blank.']], FormErrorsAttributes::getAll($formModel));
    }
}
