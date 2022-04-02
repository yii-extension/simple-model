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
        $formModel->setUserLogin('admin');
        $this->assertSame('admin', $formModel->getAttributeValue('user.login'));
    }

    public function testGetAttributeValueExceptionUndefinedAttribute(): void
    {
        $formModel = new Nested();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "profile" is not a nested attribute.');
        $formModel->getAttributeValue('profile.user');
    }

    public function testGetAttributeValueExceptionUndefinedProperty(): void
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
        $this->assertEmpty($formModel->getHint('noExist'));
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

    public function testLoadPublicField(): void
    {
        $formModel = new Nested();
        $this->assertEmpty($formModel->getname());
        $this->assertEmpty($formModel->getLastName());

        $data = [
            'Nested' => [
                'user.name' => 'joe',
                'user.lastName' => 'doe',
            ],
        ];

        $this->assertTrue($formModel->load($data));
        $this->assertSame('joe', $formModel->getName());
        $this->assertSame('doe', $formModel->getLastName());
    }
}
