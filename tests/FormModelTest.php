<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yii\Extension\Simple\Model\FormModel;
use Yii\Extension\Simple\Model\FormModelInterface;
use Yii\Extension\Simple\Model\Helper\HtmlFormErrors;
use Yii\Extension\Simple\Model\Tests\FormModel\Login;
use Yii\Extension\Simple\Model\Tests\FormModel\NestedAttribute;
use Yii\Extension\Simple\Model\Tests\FormModel\Stub;
use Yii\Extension\Simple\Model\Tests\FormModel\Type;
use Yii\Extension\Simple\Model\Tests\TestSupport\TestTrait;

require __DIR__ . '/FormModel/NonNamespaced.php';

final class FormModelTest extends TestCase
{
    use TestTrait;

    public function testGetAttributeHint(): void
    {
        $formModel = new Login();
        $this->assertSame('Write your id or email.', $formModel->getAttributeHint('login'));
        $this->assertSame('Write your password.', $formModel->getAttributeHint('password'));
        $this->assertEmpty($formModel->getAttributeHint('noExist'));

        $formModel = new NestedAttribute();
        $this->assertSame('Write your id or email.', $formModel->getAttributeHint('user.login'));
        $this->assertEmpty($formModel->getAttributeHint('noExist'));
    }

    public function testGetAttributeHints(): void
    {
        /** @var FormModelInterface $formModel */
        $formModel = new Stub();
        $this->assertSame([], $formModel->getAttributeHints());
    }

    public function testGetAttributeLabel(): void
    {
        $formModel = new Login();
        $this->assertSame('Login:', $formModel->getAttributeLabel('login'));
        $this->assertSame('Testme', $formModel->getAttributeLabel('testme'));

        $formModel = new NestedAttribute();
        $this->assertSame('Login:', $formModel->getAttributeLabel('user.login'));
    }

    public function testGetAttributeLabels(): void
    {
        $formModel = new Stub();
        $this->assertSame([], $formModel->getAttributeLabels());
    }

    public function testGetAttributePlaceHolder(): void
    {
        $formModel = new Login();
        $this->assertSame('Type Username or Email.', $formModel->getAttributePlaceHolder('login'));
        $this->assertSame('Type Password.', $formModel->getAttributePlaceHolder('password'));
        $this->assertEmpty($formModel->getAttributePlaceHolder('noExist'));
    }

    public function testGetAttributePlaceHolders(): void
    {
        $formModel = new Stub();
        $this->assertSame([], $formModel->getAttributePlaceHolders());
    }

    public function testGetNestedAttributePlaceHolder(): void
    {
        $formModel = new NestedAttribute();
        $this->assertSame('Type Username or Email.', $formModel->getAttributePlaceHolder('user.login'));
    }

    public function testGetAttributeValue(): void
    {
        $formModel = new Type();

        $formModel->setAttribute('array', [1, 2]);
        $this->assertIsArray($formModel->getAttributeValue('array'));
        $this->assertSame([1, 2], $formModel->getAttributeValue('array'));

        $formModel->setAttribute('bool', true);
        $this->assertIsBool($formModel->getAttributeValue('bool'));
        $this->assertSame(true, $formModel->getAttributeValue('bool'));

        $formModel->setAttribute('float', 1.2023);
        $this->assertIsFloat($formModel->getAttributeValue('float'));
        $this->assertSame(1.2023, $formModel->getAttributeValue('float'));

        $formModel->setAttribute('int', 1);
        $this->assertIsInt($formModel->getAttributeValue('int'));
        $this->assertSame(1, $formModel->getAttributeValue('int'));

        $formModel->setAttribute('object', new StdClass());
        $this->assertIsObject($formModel->getAttributeValue('object'));
        $this->assertInstanceOf(StdClass::class, $formModel->getAttributeValue('object'));

        $formModel->setAttribute('string', 'samdark');
        $this->assertIsString($formModel->getAttributeValue('string'));
        $this->assertSame('samdark', $formModel->getAttributeValue('string'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\Simple\Model\Tests\FormModel\Type::noExist".'
        );
        $formModel->getAttributeValue('noExist');
    }

    public function testGetAttributeValueWithNestedAttribute(): void
    {
        $formModel = new NestedAttribute();

        $formModel->setUserLogin('admin');
        $this->assertSame('admin', $formModel->getAttributeValue('user.login'));
    }

    public function testGetAttributeValueWithNestedAttributeException(): void
    {
        $formModel = new NestedAttribute();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\Simple\Model\Tests\FormModel\Login::noExist'
        );
        $formModel->getAttributeValue('user.noExist');
    }

    public function testGetFormName(): void
    {
        $formModel = new Stub();
        $this->assertSame('Stub', $formModel->getFormName());

        $formModel = new Login();
        $this->assertSame('Login', $formModel->getFormName());

        $formModel = new class () extends FormModel {
        };
        $this->assertSame('', $formModel->getFormName());

        /** @var FormModelInterface $formModel */
        $formModel = new \NonNamespaced();
        $this->assertSame('NonNamespaced', $formModel->getFormName());
    }

    public function testGetNestedAttributeException(): void
    {
        $formModel = new NestedAttribute();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "profile" is not a nested attribute.');
        $formModel->getAttributeValue('profile.user');
    }

    public function testGetIsValidated(): void
    {
        $formModel = new Stub();
        $this->assertSame(false, $formModel->isValidated());
    }

    public function testGetRules(): void
    {
        $formModel = new Stub();
        $this->assertSame([], $formModel->getRules());
    }

    public function testsFormErrorsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Model errors class must implement Yii\Extension\Simple\Model\FormErrorsInterface'
        );
        new class () extends FormModel {
            protected string $formErrorsClass = stdClass::class;
        };
    }

    public function testHasAttribute(): void
    {
        $formModel = new Login();
        $this->assertTrue($formModel->hasAttribute('login'));
        $this->assertTrue($formModel->hasAttribute('password'));
        $this->assertTrue($formModel->hasAttribute('rememberMe'));
        $this->assertFalse($formModel->hasAttribute('noExist'));
        $this->assertFalse($formModel->hasAttribute('extraField'));
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

    public function testLoadPublicFieldNested(): void
    {
        $formModel = new NestedAttribute();
        $this->assertEmpty($formModel->getname());
        $this->assertEmpty($formModel->getLastName());

        $data = [
            'NestedAttribute' => [
                'user.name' => 'joe',
                'user.lastName' => 'doe',
            ],
        ];

        $this->assertTrue($formModel->load($data));
        $this->assertSame('joe', $formModel->getName());
        $this->assertSame('doe', $formModel->getLastName());
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
        $this->assertIsInt($formModel->getAttributeValue('int'));
        $this->assertIsFloat($formModel->getAttributeValue('float'));
        $this->assertIsBool($formModel->getAttributeValue('bool'));
        $this->assertIsString($formModel->getAttributeValue('string'));
    }

    public function testSetAttribute(): void
    {
        $formModel = new Type();

        $formModel->setAttribute('array', []);
        $this->assertIsArray($formModel->getAttributeValue('array'));

        $formModel->setAttribute('bool', false);
        $this->assertIsBool($formModel->getAttributeValue('bool'));

        $formModel->setAttribute('bool', 'false');
        $this->assertIsBool($formModel->getAttributeValue('bool'));

        $formModel->setAttribute('float', 1.434536);
        $this->assertIsFloat($formModel->getAttributeValue('float'));

        $formModel->setAttribute('float', '1.434536');
        $this->assertIsFloat($formModel->getAttributeValue('float'));

        $formModel->setAttribute('int', 1);
        $this->assertIsInt($formModel->getAttributeValue('int'));

        $formModel->setAttribute('int', '1');
        $this->assertIsInt($formModel->getAttributeValue('int'));

        $formModel->setAttribute('object', new stdClass());
        $this->assertIsObject($formModel->getAttributeValue('object'));

        $formModel->setAttribute('string', '');
        $this->assertIsString($formModel->getAttributeValue('string'));
    }

    public function testSetAttributes(): void
    {
        $formModel = new Type();

        // set attributes with array and to camel case disabled.
        $formModel->setAttributes(
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
        $formModel->setAttributes(
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

    public function testSetAttributesException(): void
    {
        $formModel = new Type();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "noExist" does not exist');
        $formModel->setAttributes(['noExist' => []]);
    }

    public function testValidatorRules(): void
    {
        $formModel = new Login();
        $validator = $this->createValidator();

        $formModel->login('');
        $validator->validate($formModel);
        $this->assertSame(['Value cannot be blank.'], HtmlFormErrors::getErrors($formModel, 'login'));

        $formModel->login('x');
        $validator->validate($formModel);
        $this->assertSame(['Is too short.'], HtmlFormErrors::getErrors($formModel, 'login'));

        $formModel->login(str_repeat('x', 60));
        $validator->validate($formModel);
        $this->assertSame('Is too long.', HtmlFormErrors::getFirstError($formModel, 'login'));

        $formModel->login('admin@.com');
        $validator->validate($formModel);
        $this->assertSame('This value is not a valid email address.', HtmlFormErrors::getFirstError($formModel, 'login'));
    }
}
