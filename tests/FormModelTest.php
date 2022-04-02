<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yii\Extension\FormModel\Attribute\FormErrorsAttribute;
use Yii\Extension\FormModel\FormModel;
use Yii\Extension\FormModel\FormModelInterface;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Login;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Stub;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Type;
use Yii\Extension\FormModel\Tests\TestSupport\TestTrait;

require __DIR__ . '/TestSupport/FormModel/NonNamespaced.php';

final class FormModelTest extends TestCase
{
    use TestTrait;

    public function testGetFormName(): void
    {
        $formModel = new Stub();
        $this->assertSame('Stub', $formModel->getFormName());

        $formModel = new Login();
        $this->assertSame('Login', $formModel->getFormName());

        $formModel = new class () extends FormModel {
        };
        $this->assertSame('', $formModel->getFormName());

        $formModel = new \NonNamespaced();
        $this->assertSame('NonNamespaced', $formModel->getFormName());
    }

    public function testGetRules(): void
    {
        $formModel = new Stub();
        $this->assertSame([], $formModel->getRules());
    }

    public function testIsValidated(): void
    {
        $formModel = new Stub();
        $this->assertSame(false, $formModel->isValidated());
    }

    public function testLoad(): void
    {
        $formModel = new Login();
        $data = [];

        $formModel->load($data);
        $this->assertNull($formModel->getLogin());
        $this->assertNull($formModel->getPassword());
        $this->assertFalse($formModel->getRememberMe());

        $data = [
            'Login' => [
                'login' => 'admin',
                'password' => '123456',
                'rememberMe' => true,
                'noExist' => 'noExist',
                'extraField' => 'extra-field',
            ],
        ];

        $this->assertTrue($formModel->load($data));
        $this->assertSame('admin', $formModel->getLogin());
        $this->assertSame('123456', $formModel->getPassword());
        $this->assertSame(true, $formModel->getRememberMe());
    }

    public function testLoadPublicField(): void
    {
        $formModel = new Login();
        $this->assertEmpty($formModel->name);

        $data = [
            'Login' => [
                'name' => 'samdark',
            ],
        ];

        $this->assertTrue($formModel->load($data));
        $this->assertSame('samdark', $formModel->name);
    }

    public function testLoadWithEmptyScope(): void
    {
        $formModel = new class () extends FormModel {
            private int $int = 1;
            private string $string = 'string';
            private float $float = 3.14;
            private bool $bool = true;
        };
        $formModel->load([
            'int' => '2',
            'float' => '3.15',
            'bool' => 'false',
            'string' => 555,
        ], '');
        $this->assertIsInt($formModel->getCastValue('int'));
        $this->assertIsFloat($formModel->getCastValue('float'));
        $this->assertIsBool($formModel->getCastValue('bool'));
        $this->assertIsString($formModel->getCastValue('string'));
    }

    public function testSetsException(): void
    {
        $formModel = new Type();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "noExist" does not exist');
        $formModel->sets(['noExist' => []]);
    }
}
