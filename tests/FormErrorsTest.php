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
        $formModel->getFormErrors()->add('password', $errorMessage);
        $this->assertTrue($formModel->getFormErrors()->has('password'));
        $this->assertSame($errorMessage, $formModel->getFormErrors()->getFirst('password'));
    }

    public function testAddMultipleErrors(): void
    {
        $formModel = new Login();
        $errorMessage = ['password' => ['0' => 'Invalid password.']];
        $formModel->getFormErrors()->clear();
        $this->assertEmpty($formModel->getFormErrors()->getFirst('password'));

        $formModel->getFormErrors()->addMultiple($errorMessage);
        $this->assertTrue($formModel->getFormErrors()->has('password'));
        $this->assertSame('Invalid password.', $formModel->getFormErrors()->getFirst('password'));
    }

    public function testGet(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->getFormErrors()->get('password'));
    }

    public function testGetAll(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->getFormErrors()->getAll());
    }

    public function testGetFirst(): void
    {
        $formModel = new Login();
        $this->assertSame('', $formModel->getFormErrors()->getFirst('password'));
    }

    public function testGetFirsts(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->getFormErrors()->getFirsts());
    }

    public function testGetSummary(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->getFormErrors()->getSummary());
    }

    public function testGetSummaryFirst(): void
    {
        $formModel = new Login();
        $this->assertSame([], $formModel->getFormErrors()->getSummaryFirst());
    }

    public function testHas(): void
    {
        $formModel = new Login();
        $this->assertSame(false, $formModel->getFormErrors()->has('password'));
    }
}
