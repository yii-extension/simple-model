<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Simple\Model\Helper\HtmlErrors;
use Yii\Extension\Simple\Model\Tests\Model\LoginModel;

final class ModelErrorsTest extends TestCase
{
    public function testAddError(): void
    {
        $model = new LoginModel();
        $errorMessage = 'Invalid password.';

        $model->getModelErrors()->addError('password', $errorMessage);
        $this->assertTrue($model->getModelErrors()->hasErrors('password'));
        $this->assertSame($errorMessage, $model->getModelErrors()->getFirstError('password'));
    }

    public function testAddErrors(): void
    {
        $model = new LoginModel();
        $errorMessage = ['password' => ['0' => 'Invalid password.']];

        $model->getModelErrors()->addErrors($errorMessage);
        $this->assertTrue($model->getModelErrors()->hasErrors('password'));
        $this->assertSame('Invalid password.', $model->getModelErrors()->getFirstError('password'));
    }

    public function testGetAllErrors(): void
    {
        $model = new LoginModel();
        $this->assertSame('', $model->getModelErrors()->getFirstError('password'));
    }

    public function testGetErrors(): void
    {
        $model = new LoginModel();
        $this->assertSame([], $model->getModelErrors()->getErrors('password'));
    }

    public function testGetErrorSummary(): void
    {
        $model = new LoginModel();
        $this->assertSame([], $model->getModelErrors()->getErrorSummary());
    }

    public function testGetErrorSummaryFirstErrors(): void
    {
        $model = new LoginModel();
        $this->assertSame([], $model->getModelErrors()->getErrorSummaryFirstErrors());
    }

    public function testGetFirstError(): void
    {
        $model = new LoginModel();
        $this->assertSame('', $model->getModelErrors()->getFirstError('password'));
    }

    public function testGetFirstErrors(): void
    {
        $model = new LoginModel();
        $this->assertSame([], $model->getModelErrors()->getFirstErrors());
    }

    public function testHasErrors(): void
    {
        $model = new LoginModel();
        $this->assertSame(false, $model->getModelErrors()->hasErrors('password'));
    }
}
