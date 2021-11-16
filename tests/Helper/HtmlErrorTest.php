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

    public function testGetAllErrors(): void
    {
        $model = new LoginModel();

        $data = [
            'LoginModel' => [
                'login' => 'admin@.com',
                'password' => '123456',
            ],
        ];

        $expected = [
            'login' => ['This value is not a valid email address.'],
            'password' => ['Is too short.'],
        ];

        $validator = $this->createValidator();

        $this->assertTrue($model->load($data));
        $this->assertFalse($validator->validate($model)->isValid());

        // check if all errors are returned
        $this->assertTrue(HtmlErrors::hasErrors($model));

        // get all errors
        $this->assertSame(
            $expected,
            HtmlErrors::getAllErrors($model),
        );

        // get errors for specific attribute
        $this->assertSame(
            ['This value is not a valid email address.'],
            HtmlErrors::getErrors($model, 'login'),
        );

        // get error summary first errors for specific attribute
        $this->assertSame(
            'This value is not a valid email address.',
            HtmlErrors::getFirstError($model, 'login'),
        );

        // get error sumamary all errors
        $this->assertSame(
            ['This value is not a valid email address.', 'Is too short.'],
            HtmlErrors::getErrorSummary($model),
        );

        // get first error
        $this->assertSame(
            'This value is not a valid email address.',
            HtmlErrors::getFirstError($model, 'login'),
        );
    }
}
