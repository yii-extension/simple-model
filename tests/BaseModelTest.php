<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use stdClass;
use Yii\Extension\Simple\Model\Helper\HtmlErrors;
use Yii\Extension\Simple\Model\Tests\Model\LoginModel;
use Yii\Extension\Simple\Model\Tests\Model\NestedAttributeModel;
use Yii\Extension\Simple\Model\Tests\Model\StubModel;
use Yii\Extension\Simple\Model\Tests\Model\TypeModel;
use Yii\Extension\Simple\Model\Tests\TestSupport\TestTrait;

use function sprintf;

final class BaseModelTest extends TestCase
{
    use TestTrait;

    public function testAddError(): void
    {
        $model = new LoginModel();
        $errorMessage = 'Invalid password.';

        $model->getFormErrors()->addError('password', $errorMessage);
        $this->assertTrue(HtmlErrors::hasErrors($model));
        $this->assertSame($errorMessage, HtmlErrors::getFirstError($model, 'password'));
    }

    public function testGetAttributeHint(): void
    {
        $model = new LoginModel();
        $this->assertEquals('Write your id or email.', $model->getAttributeHint('login'));
        $this->assertEquals('Write your password.', $model->getAttributeHint('password'));
        $this->assertEmpty($model->getAttributeHint('noExist'));

        $model = new NestedAttributeModel();
        $this->assertEquals('Write your id or email.', $model->getAttributeHint('user.login'));
        $this->assertEmpty($model->getAttributeHint('noExist'));
    }

    public function testGetAttributeLabel(): void
    {
        $model = new LoginModel();
        $this->assertEquals('Login:', $model->getAttributeLabel('login'));
        $this->assertEquals('Testme', $model->getAttributeLabel('testme'));

        $model = new NestedAttributeModel();
        $this->assertEquals('Login:', $model->getAttributeLabel('user.login'));
    }

    public function testGetAttributePlaceHolder(): void
    {
        $model = new LoginModel();
        $this->assertEquals('Type Usernamer or Email.', $model->getAttributePlaceHolder('login'));
        $this->assertEquals('Type Password.', $model->getAttributePlaceHolder('password'));
        $this->assertEmpty($model->getAttributePlaceHolder('noExist'));
    }

    public function testGetNestedAttributePlaceHolder(): void
    {
        $model = new NestedAttributeModel();
        $this->assertEquals('Type Usernamer or Email.', $model->getAttributePlaceHolder('user.login'));
    }

    public function testGetAttributeValue(): void
    {
        $model = new TypeModel();

        $model->setAttribute('array', [1, 2]);
        $this->assertIsArray($model->getAttributeValue('array'));
        $this->assertSame([1, 2], $model->getAttributeValue('array'));

        $model->setAttribute('bool', true);
        $this->assertIsBool($model->getAttributeValue('bool'));
        $this->assertSame(true, $model->getAttributeValue('bool'));

        $model->setAttribute('float', 1.2023);
        $this->assertIsFloat($model->getAttributeValue('float'));
        $this->assertSame(1.2023, $model->getAttributeValue('float'));

        $model->setAttribute('int', 1);
        $this->assertIsInt($model->getAttributeValue('int'));
        $this->assertSame(1, $model->getAttributeValue('int'));

        $model->setAttribute('object', new StdClass());
        $this->assertIsObject($model->getAttributeValue('object'));
        $this->assertInstanceOf(StdClass::class, $model->getAttributeValue('object'));

        $model->setAttribute('string', 'samdark');
        $this->assertIsString($model->getAttributeValue('string'));
        $this->assertSame('samdark', $model->getAttributeValue('string'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\Simple\Model\Tests\Model\TypeModel::noExist".'
        );
        $model->getAttributeValue('noExist');
    }

    public function testGetAttributeValueWithNestedAttribute(): void
    {
        $model = new NestedAttributeModel();

        $model->setUserLogin('admin');
        $this->assertEquals('admin', $model->getAttributeValue('user.login'));
    }

    public function testGetNestedAttributeException(): void
    {
        $model = new NestedAttributeModel();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "profile" is not a nested attribute.');
        $model->getAttributeValue('profile.user');
    }

    public function testGetAttributeValueWithNestedAttributeException(): void
    {
        $model = new NestedAttributeModel();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\Simple\Model\Tests\Model\LoginModel::noExist'
        );
        $model->getAttributeValue('user.noExist');
    }

    public function testGetFormName(): void
    {
        $model = new StubModel();
        $this->assertEquals('StubModel', $model->getFormName());

        $model = new LoginModel();
        $this->assertEquals('LoginModel', $model->getFormName());
    }

    public function testGetRules(): void
    {
        $model = new StubModel();
        $this->assertSame([], $model->getRules());
    }

    public function testHasAttribute(): void
    {
        $model = new LoginModel();
        $this->assertTrue($model->hasAttribute('login'));
        $this->assertTrue($model->hasAttribute('password'));
        $this->assertTrue($model->hasAttribute('rememberMe'));
        $this->assertFalse($model->hasAttribute('noExist'));
        $this->assertFalse($model->hasAttribute('extraField'));
    }

    public function testLoad(): void
    {
        $model = new LoginModel();
        $data = [];

        $model->load($data);
        $this->assertNull($model->getLogin());
        $this->assertNull($model->getPassword());
        $this->assertFalse($model->getRememberMe());

        $data = [
            'LoginModel' => [
                'login' => 'admin',
                'password' => '123456',
                'rememberMe' => true,
                'noExist' => 'noExist',
                'extraField' => 'extra-field',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertEquals('admin', $model->getLogin());
        $this->assertEquals('123456', $model->getPassword());
        $this->assertEquals(true, $model->getRememberMe());
    }

    public function testLoadPublicField(): void
    {
        $model = new LoginModel();
        $this->assertEmpty($model->name);

        $data = [
            'LoginModel' => [
                'name' => 'samdark',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertEquals('samdark', $model->name);
    }

    public function testLoadPublicFieldNested(): void
    {
        $model = new NestedAttributeModel();
        $this->assertEmpty($model->getname());
        $this->assertEmpty($model->getLastName());

        $data = [
            'NestedAttributeModel' => [
                'user.name' => 'joe',
                'user.lastName' => 'doe',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertEquals('joe', $model->getName());
        $this->assertEquals('doe', $model->getLastName());
    }

    public function testSetAttribute(): void
    {
        $model = new TypeModel();

        $model->setAttribute('array', []);
        $this->assertIsArray($model->getAttributeValue('array'));

        $model->setAttribute('bool', false);
        $this->assertIsBool($model->getAttributeValue('bool'));

        $model->setAttribute('bool', 'false');
        $this->assertIsBool($model->getAttributeValue('bool'));

        $model->setAttribute('float', 1.434536);
        $this->assertIsFloat($model->getAttributeValue('float'));

        $model->setAttribute('float', '1.434536');
        $this->assertIsFloat($model->getAttributeValue('float'));

        $model->setAttribute('int', 1);
        $this->assertIsInt($model->getAttributeValue('int'));

        $model->setAttribute('int', '1');
        $this->assertIsInt($model->getAttributeValue('int'));

        $model->setAttribute('object', new stdClass());
        $this->assertIsObject($model->getAttributeValue('object'));

        $model->setAttribute('string', '');
        $this->assertIsString($model->getAttributeValue('string'));
    }

    public function testSetAttributes(): void
    {
        $model = new TypeModel();

        // set attributes with array and to camel case disabled.
        $model->setAttributes(
            [
                'array' => [],
                'bool' => false,
                'float' => 1.434536,
                'int' => 1,
                'object' => new stdClass(),
                'string' => '',
                'toCamelCase' => '',
            ],
            false,
        );

        $this->assertIsArray($model->getAttributeValue('array'));
        $this->assertIsBool($model->getAttributeValue('bool'));
        $this->assertIsFloat($model->getAttributeValue('float'));
        $this->assertIsInt($model->getAttributeValue('int'));
        $this->assertIsObject($model->getAttributeValue('object'));
        $this->assertIsString($model->getAttributeValue('string'));
        $this->assertIsString($model->getAttributeValue('toCamelCase'));

        // set attributes with array and to camel case enabled.
        $model->setAttributes(
            [
                'array' => [],
                'bool' => 'false',
                'float' => '1.434536',
                'int' => '1',
                'object' => new stdClass(),
                'string' => '',
                'to_camel_case' => '',
            ],
            true,
        );

        $this->assertIsArray($model->getAttributeValue('array'));
        $this->assertIsBool($model->getAttributeValue('bool'));
        $this->assertIsFloat($model->getAttributeValue('float'));
        $this->assertIsInt($model->getAttributeValue('int'));
        $this->assertIsObject($model->getAttributeValue('object'));
        $this->assertIsString($model->getAttributeValue('string'));
        $this->assertIsString($model->getAttributeValue('toCamelCase'));
    }

    public function testSetAttributesException(): void
    {
        $model = new TypeModel();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute "noExist" does not exist');
        $model->setAttributes(['noExist' => []], false);
    }

    public function testValidatorRules(): void
    {
        $model = new LoginModel();
        $validator = $this->createValidator();

        $model->login('');
        $validator->validate($model);
        $this->assertEquals(['Value cannot be blank.'], HtmlErrors::getErrors($model, 'login'));

        $model->login('x');
        $validator->validate($model);
        $this->assertEquals(['Is too short.'], HtmlErrors::getErrors($model, 'login'));

        $model->login(str_repeat('x', 60));
        $validator->validate($model);
        $this->assertEquals('Is too long.', HtmlErrors::getFirstError($model, 'login'));

        $model->login('admin@.com');
        $validator->validate($model);
        $this->assertEquals('This value is not a valid email address.', HtmlErrors::getFirstError($model, 'login'));
    }
}
