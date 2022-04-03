<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StdClass;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Login;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Stub;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Type;

final class FormModelAttributesTest extends TestCase
{
    public function testGetAttributeValue(): void
    {
        $formModel = new Type();

        $formModel->set('array', [1, 2]);
        $this->assertIsArray($formModel->getAttributeValue('array'));
        $this->assertSame([1, 2], $formModel->getAttributeValue('array'));

        $formModel->set('bool', true);
        $this->assertIsBool($formModel->getAttributeValue('bool'));
        $this->assertSame(true, $formModel->getAttributeValue('bool'));

        $formModel->set('float', 1.2023);
        $this->assertIsFloat($formModel->getAttributeValue('float'));
        $this->assertSame(1.2023, $formModel->getAttributeValue('float'));

        $formModel->set('int', 1);
        $this->assertIsInt($formModel->getAttributeValue('int'));
        $this->assertSame(1, $formModel->getAttributeValue('int'));

        $formModel->set('object', new StdClass());
        $this->assertIsObject($formModel->getAttributeValue('object'));
        $this->assertInstanceOf(StdClass::class, $formModel->getAttributeValue('object'));

        $formModel->set('string', 'samdark');
        $this->assertIsString($formModel->getAttributeValue('string'));
        $this->assertSame('samdark', $formModel->getAttributeValue('string'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\FormModel\Tests\TestSupport\FormModel\Type::noExist".'
        );
        $formModel->getAttributeValue('noExist');
    }

    public function testGetHint(): void
    {
        $formModel = new Login();
        $this->assertSame('Write your id or email.', $formModel->getHint('login'));
        $this->assertSame('Write your password.', $formModel->getHint('password'));
    }

    public function testGetHintException(): void
    {
        $formModel = new Login();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        $formModel->getHint('noExist');
    }

    public function testGetHints(): void
    {
        $formModel = new Stub();
        $this->assertSame([], $formModel->getHints());
    }

    public function testGetLabel(): void
    {
        $formModel = new Login();
        $this->assertSame('Login:', $formModel->getLabel('login'));
    }

    public function testGetLabelException(): void
    {
        $formModel = new Login();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        $formModel->getLabel('noExist');
    }

    public function testGetLabels(): void
    {
        $formModel = new Stub();
        $this->assertSame([], $formModel->getLabels());
    }

    public function testGetPlaceHolder(): void
    {
        $formModel = new Login();
        $this->assertSame('Type Username or Email.', $formModel->getPlaceHolder('login'));
        $this->assertSame('Type Password.', $formModel->getPlaceHolder('password'));
    }

    public function testGetPlaceException(): void
    {
        $formModel = new Login();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        $formModel->getPlaceHolder('noExist');
    }

    public function testGetPlaceHolders(): void
    {
        $formModel = new Stub();
        $this->assertSame([], $formModel->getPlaceHolders());
    }

    public function testHas(): void
    {
        $formModel = new Login();
        $this->assertTrue($formModel->has('login'));
        $this->assertTrue($formModel->has('password'));
        $this->assertTrue($formModel->has('rememberMe'));
        $this->assertFalse($formModel->has('noExist'));
        $this->assertFalse($formModel->has('extraField'));
    }

    public function testSet(): void
    {
        $formModel = new Type();

        $formModel->set('array', []);
        $this->assertIsArray($formModel->getAttributeValue('array'));

        $formModel->set('bool', false);
        $this->assertIsBool($formModel->getAttributeValue('bool'));

        $formModel->set('bool', 'false');
        $this->assertIsBool($formModel->getAttributeValue('bool'));

        $formModel->set('float', 1.434536);
        $this->assertIsFloat($formModel->getAttributeValue('float'));

        $formModel->set('float', '1.434536');
        $this->assertIsFloat($formModel->getAttributeValue('float'));

        $formModel->set('int', 1);
        $this->assertIsInt($formModel->getAttributeValue('int'));

        $formModel->set('int', '1');
        $this->assertIsInt($formModel->getAttributeValue('int'));

        $formModel->set('object', new stdClass());
        $this->assertIsObject($formModel->getAttributeValue('object'));

        $formModel->set('string', '');
        $this->assertIsString($formModel->getAttributeValue('string'));
    }

    public function testSets(): void
    {
        $formModel = new Type();

        // set attributes with array and to camel case disabled.
        $formModel->sets(
            [
                'array' => [],
                'bool' => false,
                'float' => 1.434536,
                'int' => 1,
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        $this->assertIsArray($formModel->getAttributeValue('array'));
        $this->assertIsBool($formModel->getAttributeValue('bool'));
        $this->assertIsFloat($formModel->getAttributeValue('float'));
        $this->assertIsInt($formModel->getAttributeValue('int'));
        $this->assertIsObject($formModel->getAttributeValue('object'));
        $this->assertIsString($formModel->getAttributeValue('string'));

        // set attributes with array and to camel case enabled.
        $formModel->sets(
            [
                'array' => [],
                'bool' => 'false',
                'float' => '1.434536',
                'int' => '1',
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        $this->assertIsArray($formModel->getAttributeValue('array'));
        $this->assertIsBool($formModel->getAttributeValue('bool'));
        $this->assertIsFloat($formModel->getAttributeValue('float'));
        $this->assertIsInt($formModel->getAttributeValue('int'));
        $this->assertIsObject($formModel->getAttributeValue('object'));
        $this->assertIsString($formModel->getAttributeValue('string'));
    }

    public function testSetsException(): void
    {
        $formModel = new Type();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "noExist" does not exist');
        $formModel->sets(['noExist' => []]);
    }
}
