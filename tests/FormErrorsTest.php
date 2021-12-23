<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Simple\Model\Tests\FormModel\Login;

final class FormErrorsTest extends TestCase
{
    public function testAddError(): void
    {
        $model = new Login();
        $errorMessage = 'Invalid password.';

        $model->getFormErrors()->addError('password', $errorMessage);
        $this->assertTrue($model->getFormErrors()->hasErrors('password'));
        $this->assertSame($errorMessage, $model->getFormErrors()->getFirstError('password'));
    }

    public function testAddErrors(): void
    {
        $model = new Login();
        $errorMessage = ['password' => ['0' => 'Invalid password.']];

        $model->getFormErrors()->clear();
        $this->assertEmpty($model->getFormErrors()->getFirstError('password'));

        $model->getFormErrors()->addErrors($errorMessage);
        $this->assertTrue($model->getFormErrors()->hasErrors('password'));
        $this->assertSame('Invalid password.', $model->getFormErrors()->getFirstError('password'));
    }

    public function testGetAllErrors(): void
    {
        $model = new Login();
        $this->assertSame('', $model->getFormErrors()->getFirstError('password'));
    }

    public function testGetErrors(): void
    {
        $model = new Login();
        $this->assertSame([], $model->getFormErrors()->getErrors('password'));
    }

    public function testGetErrorSummary(): void
    {
        $model = new Login();
        $this->assertSame([], $model->getFormErrors()->getErrorSummary());
    }

    public function testGetErrorSummaryFirstErrors(): void
    {
        $model = new Login();
        $this->assertSame([], $model->getFormErrors()->getErrorSummaryFirstErrors());
    }

    public function testGetFirstError(): void
    {
        $model = new Login();
        $this->assertSame('', $model->getFormErrors()->getFirstError('password'));
    }

    public function testGetFirstErrors(): void
    {
        $model = new Login();
        $this->assertSame([], $model->getFormErrors()->getFirstErrors());
    }

    public function testHasErrors(): void
    {
        $model = new Login();
        $this->assertSame(false, $model->getFormErrors()->hasErrors('password'));
    }
}
