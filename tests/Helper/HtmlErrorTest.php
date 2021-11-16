<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Simple\Model\Helper\HtmlErrors;
use Yii\Extension\Simple\Model\Tests\Model\LoginModel;
use Yii\Extension\Simple\Model\Tests\TestSupport\TestTrait;

final class HtmlErrorTest extends TestCase
{
    use TestTrait;

    private array $data = [
        'LoginModel' => [
            'login' => 'admin@.com',
            'password' => '123456',
        ],
    ];
    private array $expected = [
        'login' => ['This value is not a valid email address.'],
        'password' => ['Is too short.'],
    ];
    private LoginModel $model;

    public function testGetAllErrors(): void
    {
        $validator = $this->createValidator();
        $this->assertTrue($this->model->load($this->data));
        $this->assertFalse($validator->validate($this->model)->isValid());
        $this->assertSame($this->expected, HtmlErrors::getAllErrors($this->model));
    }

    public function testGetErrors(): void
    {
        $validator = $this->createValidator();
        $this->assertTrue($this->model->load($this->data));
        $this->assertFalse($validator->validate($this->model)->isValid());
        $this->assertSame(['This value is not a valid email address.'], HtmlErrors::getErrors($this->model, 'login'));
    }

    public function testGetErrorSummary(): void
    {
        $validator = $this->createValidator();
        $this->assertTrue($this->model->load($this->data));
        $this->assertFalse($validator->validate($this->model)->isValid());
        $this->assertSame(
            ['This value is not a valid email address.', 'Is too short.'],
            HtmlErrors::getErrorSummary($this->model),
        );
    }

    public function testGetErrorSummaryFirstErrors(): void
    {
        $validator = $this->createValidator();
        $this->assertTrue($this->model->load($this->data));
        $this->assertFalse($validator->validate($this->model)->isValid());
        $this->assertSame(
            ['login' => 'This value is not a valid email address.', 'password' => 'Is too short.'],
            HtmlErrors::getErrorSummaryFirstErrors($this->model),
        );
    }

    public function testGetFirstError(): void
    {
        $validator = $this->createValidator();
        $this->assertTrue($this->model->load($this->data));
        $this->assertFalse($validator->validate($this->model)->isValid());
        $this->assertSame('This value is not a valid email address.', HtmlErrors::getFirstError($this->model, 'login'));
    }

    public function testGetFirstErrorEmpty(): void
    {
        $this->assertSame('', HtmlErrors::getFirstError($this->model, 'login'));
    }

    public function testGetFirstErrorsEmpty(): void
    {
        $this->assertSame([], HtmlErrors::getFirstErrors($this->model));
    }

    public function testHasError(): void
    {
        $validator = $this->createValidator();
        $this->assertTrue($this->model->load($this->data));
        $this->assertFalse($validator->validate($this->model)->isValid());
        $this->assertTrue(HtmlErrors::hasErrors($this->model));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new LoginModel();
    }
}
