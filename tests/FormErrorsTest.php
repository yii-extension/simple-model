<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Login;

final class FormErrorsTest extends TestCase
{
    public function testAdd(): void
    {
        $formModel = new Login();
        $errorMessage = 'Invalid password.';
        $formModel->error()->add('password', $errorMessage);
        $this->assertTrue($formModel->error()->has('password'));
        $this->assertSame($errorMessage, $formModel->error()->getFirst('password'));
    }

    public function testAddMultipleErrors(): void
    {
        $formModel = new Login();
        $errorMessage = ['password' => ['0' => 'Invalid password.']];
        $formModel->error()->clear();
        $this->assertEmpty($formModel->error()->getFirst('password'));

        $formModel->error()->addMultiple($errorMessage);
        $this->assertTrue($formModel->error()->has('password'));
        $this->assertSame('Invalid password.', $formModel->error()->getFirst('password'));
    }

    public function testGet(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->error()->get('password'));
    }

    public function testGetAll(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->error()->getAll());
    }

    public function testGetFirst(): void
    {
        $formModel = new Login();
        $this->assertSame('', $formModel->error()->getFirst('password'));
    }

    public function testGetFirsts(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->error()->getFirsts());
    }

    public function testGetSummary(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->error()->getSummary());
    }

    public function testGetSummaryFirst(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->error()->getSummaryFirst());
    }

    public function testHas(): void
    {
        $formModel = new Login();
        $this->assertSame(false, $formModel->error()->has('password'));
    }
}
