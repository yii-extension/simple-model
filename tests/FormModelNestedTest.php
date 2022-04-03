<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Nested;

final class FormModelNestedTest extends TestCase
{
    public function testGetAttributeValue(): void
    {
        $formModel = new Nested();
        $formModel->set('user.login', 'admin');
        $this->assertSame('admin', $formModel->getAttributeValue('user.login'));
    }

    public function testGetAttributeValueNotNestedException(): void
    {
        $formModel = new Nested();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "profile" is not a nested attribute.');
        $formModel->getAttributeValue('profile.user');
    }

    public function testGetAttributeValueUndefinedPropertyException(): void
    {
        $formModel = new Nested();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\FormModel\Tests\TestSupport\FormModel\Login::noExist'
        );
        $formModel->getAttributeValue('user.noExist');
    }

    public function testGetHint(): void
    {
        $formModel = new Nested();
        $this->assertSame('Write your id or email.', $formModel->getHint('user.login'));
    }

    public function testGetLabel(): void
    {
        $formModel = new Nested();
        $this->assertSame('Login:', $formModel->getLabel('user.login'));
    }

    public function testGetPlaceHolder(): void
    {
        $formModel = new Nested();
        $this->assertSame('Type Username or Email.', $formModel->getPlaceHolder('user.login'));
    }

    public function testHasException(): void
    {
        $form = new Nested();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\FormModel\Tests\TestSupport\FormModel\Login::noExist'
        );
        $form->has('user.noExist');
    }

    public function testLoadPublicField(): void
    {
        $formModel = new Nested();
        $this->assertEmpty($formModel->getAttributeValue('user.login'));
        $this->assertEmpty($formModel->getAttributeValue('user.password'));

        $data = [
            'Nested' => [
                'user.login' => 'joe',
                'user.password' => '123456',
            ],
        ];

        $this->assertTrue($formModel->load($data));
        $this->assertSame('joe', $formModel->getAttributeValue('user.login'));
        $this->assertSame('123456', $formModel->getAttributeValue('user.password'));
    }
}
