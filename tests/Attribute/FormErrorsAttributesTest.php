<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormErrorsAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Login;
use Yii\Extension\FormModel\Tests\TestSupport\TestTrait;

final class FormErrorsAttributesTest extends TestCase
{
    use TestTrait;

    private array $data = [
        'Login' => [
            'login' => 'admin@.com',
            'password' => '123456',
        ],
    ];
    private array $expected = [
        'login' => ['This value is not a valid email address.'],
        'password' => ['Is too short.'],
    ];

    public function testClearAllErrors(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertSame($this->expected, FormErrorsAttributes::getAll($formModel));
        $this->assertEmpty($formModel->getFormErrors()->clear());
    }

    public function testClearForAttribute(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertSame($this->expected, FormErrorsAttributes::getAll($formModel));
        $this->assertEmpty($formModel->getFormErrors()->clear('login'));
        $this->assertSame(['password' => ['Is too short.']], FormErrorsAttributes::getAll($formModel));
    }

    public function testGet(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertSame(
            ['This value is not a valid email address.'],
            FormErrorsAttributes::get($formModel, 'login')
        );
    }

    public function testGetAll(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertSame($this->expected, FormErrorsAttributes::getAll($formModel));
    }

    public function testGetFirst(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertSame(
            'This value is not a valid email address.',
            FormErrorsAttributes::getFirst($formModel, 'login'),
        );
    }

    public function testGetFirstEmpty(): void
    {
        $formModel = new Login();
        $this->assertSame('', FormErrorsAttributes::getFirst($formModel, 'login'));
    }

    public function testGetFirstsEmpty(): void
    {
        $formModel = new Login();
        $this->assertSame([], FormErrorsAttributes::getFirsts($formModel));
    }

    public function testGetSummary(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertSame(
            ['This value is not a valid email address.', 'Is too short.'],
            FormErrorsAttributes::getSummary($formModel),
        );
    }

    public function testGetSummaryFirst(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertSame(
            ['login' => 'This value is not a valid email address.', 'password' => 'Is too short.'],
            FormErrorsAttributes::getSummaryFirst($formModel),
        );
    }

    public function testHas(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();
        $this->assertTrue($formModel->load($this->data));
        $this->assertFalse($validator->validate($formModel)->isValid());
        $this->assertTrue(FormErrorsAttributes::has($formModel));
    }
}
