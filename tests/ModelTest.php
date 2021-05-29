<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests;

use stdClass;
use InvalidArgumentException;
use Yii\Extension\Simple\Model\AttributeModel;
use Yii\Extension\Simple\Model\Tests\Stub\ErrorModelStub;
use Yii\Extension\Simple\Model\Tests\Stub\LoginModelStub;
use Yii\Extension\Simple\Model\Tests\Stub\ModelStub;
use Yii\Extension\Simple\Model\Tests\Stub\NestedAttributeModelStub;
use Yii\Extension\Simple\Model\Tests\Stub\TypeModelStub;

use function sprintf;

final class ModelTest extends TestCase
{
    public function testAddError(): void
    {
        $model = new LoginModelStub();
        $errorMessage = 'Invalid password.';
        $model->addError('password', $errorMessage);
        $this->assertTrue($model->hasErrors());
        $this->assertEquals($errorMessage, $model->getFirstError('password'));
    }

    public function testClearErrors(): void
    {
        $errorMessage = 'Invalid password.';

        $model = new LoginModelStub();

        $model->addError('password', $errorMessage);
        $this->invokeMethod($model, 'clearErrors', ['password']);
        $this->assertEmpty($model->getError('password'));

        $model->addError('password', $errorMessage);
        $this->invokeMethod($model, 'clearErrors');
        $this->assertEmpty($model->getErrors());
    }

    public function testGetAttributes(): void
    {
        $attributeModel = $this->invokeMethod(new ModelStub(), 'getAttributes');
        $this->assertSame(
            ['public' => 'string', 'protected' => 'string', 'private' => 'string'],
            $attributeModel,
        );
    }

    public function testGetAttributeHint(): void
    {
        $model = new LoginModelStub();
        $this->assertEquals('Write your id or email.', $model->getAttributeHint('login'));
        $this->assertEquals('Write your password.', $model->getAttributeHint('password'));
        $this->assertEmpty($model->getAttributeHint('noExist'));

        $model = new NestedAttributeModelStub();
        $this->assertEquals('Write your id or email.', $model->getAttributeHint('user.login'));

        $model = new ModelStub();
        $this->assertEmpty($model->getAttributeHint('noExist'));
    }

    public function testGetAttributeLabel(): void
    {
        $model = new LoginModelStub();
        $this->assertEquals('Login:', $model->getAttributeLabel('login'));
        $this->assertEquals('Testme', $model->getAttributeLabel('testme'));

        $model = new NestedAttributeModelStub();
        $this->assertEquals('Login:', $model->getAttributeLabel('user.login'));

        $model = new ModelStub();
        $this->assertEquals('Public', $model->getAttributeLabel('public'));
    }

    public function testGetAttributeValue(): void
    {
        $model = new LoginModelStub();
        $model->login('admin');
        $this->assertEquals('admin', $model->getAttributeValue('login'));

        $model->password('123456');
        $this->assertEquals('123456', $model->getAttributeValue('password'));

        $model->rememberMe(true);
        $this->assertEquals(true, $model->getAttributeValue('rememberMe'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "Yii\Extension\Simple\Model\Tests\Stub\LoginModelStub::noExist".'
        );
        $model->getAttributeValue('noExist');
    }

    public function testGetAttributeValueWithNestedAttribute(): void
    {
        $model = new NestedAttributeModelStub();
        $model->setUserLogin('admin');
        $this->assertEquals('admin', $model->getAttributeValue('user.login'));
    }

    public function testGetAttributeValueWithNestedAttributeException(): void
    {
        $model = new NestedAttributeModelStub();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Nested attribute can only be of Yii\Extension\Simple\Model\ModelInterface type.'
        );
        $model->getAttributeValue('stubClass.name');
    }

    public function testGetError(): void
    {
        $model = new ModelStub();
        $this->assertEmpty($model->getError('public'));
    }

    public function testGetErrors(): void
    {
        $model = new ModelStub();
        $this->assertEmpty($model->getErrors());
    }

    public function testGetFirstError(): void
    {
        $model = new ModelStub();

        $model->addError('public', 'Public error');
        $model->addError('private', 'Private error');
        $this->assertSame('Public error', $model->getFirstError('public'));
        $this->assertEmpty($model->getFirstError('noExist'));
    }

    public function testGetFirstErrors(): void
    {
        $model = new ModelStub();
        $this->assertEmpty($model->getFirstErrors());

        $model->addError('public', 'Public error');
        $model->addError('private', 'Private error');
        $this->assertSame(['public' => 'Public error', 'private' => 'Private error'], $model->getFirstErrors());
    }

    public function testGetErrorSummary(): void
    {
        $model = new ModelStub();
        $this->assertEmpty($model->getErrorSummary());

        $model->addError('public', 'Public error');
        $model->addError('private', 'Private error');
        $this->assertSame(['Public error', 'Private error'], $model->getErrorSummary());
        $this->assertSame(['public' => 'Public error', 'private' => 'Private error'], $model->getErrorSummary(false));
    }

    public function testGetFormName(): void
    {
        $model = new ModelStub();
        $this->assertEquals('ModelStub', $model->getFormName());

        $model = new LoginModelStub();
        $this->assertEquals('LoginModel', $model->getFormName());
    }

    public function testHasAttribute(): void
    {
        $model = new LoginModelStub();
        $this->assertTrue($model->hasAttribute('login'));
        $this->assertTrue($model->hasAttribute('password'));
        $this->assertTrue($model->hasAttribute('rememberMe'));
        $this->assertFalse($model->hasAttribute('noExist'));
        $this->assertFalse($model->hasAttribute('extraField'));
    }

    public function testLoad(): void
    {
        $model = new LoginModelStub();

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
        $model = new LoginModelStub();
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
        $model = new NestedAttributeModelStub();
        $this->assertEmpty($model->getname());
        $this->assertEmpty($model->getLastName());

        $data = [
            'NestedAttributeModelStub' => [
                'user.name' => 'joe',
                'user.lastName' => 'doe',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertEquals('joe', $model->getName());
        $this->assertEquals('doe', $model->getLastName());
    }

    public function testSetAttributes(): void
    {
        $model = new TypeModelStub();

        $model->setAttribute('array', []);
        $this->assertIsArray($model->getAttributeValue('array'));

        $model->setAttribute('bool', false);
        $this->assertIsBool($model->getAttributeValue('bool'));

        $model->setAttribute('float', 1.434536);
        $this->assertIsFloat($model->getAttributeValue('float'));

        $model->setAttribute('int', 1);
        $this->assertIsInt($model->getAttributeValue('int'));

        $model->setAttribute('object', new stdClass());
        $this->assertIsObject($model->getAttributeValue('object'));

        $model->setAttribute('string', '');
        $this->assertIsString($model->getAttributeValue('string'));
    }

    public function testUnknownPropertyType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/You must specify the type hint for "%s" property in "([^"]+)" class./',
                'property',
            )
        );
        $model = new ErrorModelStub();
    }
}
