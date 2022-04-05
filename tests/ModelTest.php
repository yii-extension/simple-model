<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Model as AbstractModel;
use Yii\Extension\Model\Tests\TestSupport\Error\CustomFormErrors;
use Yii\Extension\Model\Tests\TestSupport\Model\Model;
use Yii\Extension\Model\Tests\TestSupport\Model\Rules;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

require __DIR__ . '/TestSupport/Model/NonNamespaced.php';

final class ModelTest extends TestCase
{
    public function testAttributes(): void
    {
        $model = new Model();
        $this->assertSame(['public', 'login', 'password'], $model->attributes());
    }

    public function testGetAttributeValue(): void
    {
        $model = new Model();
        $model->set('login', 'admin');
        $model->set('password', '123456');
        $this->assertSame('admin', $model->getAttributeValue('login'));
        $this->assertSame('123456', $model->getAttributeValue('password'));
    }

    public function testGetFormName(): void
    {
        $model = new Model();
        $this->assertSame('Model', $model->getFormName());

        $model = new class () extends AbstractModel {
        };
        $this->assertSame('', $model->getFormName());

        $model = new \NonNamespaced();
        $this->assertSame('NonNamespaced', $model->getFormName());
    }

    public function testGetRules(): void
    {
        $model = new Model();
        $this->assertSame([], $model->getRules());
    }

    public function testGetRulesWithAttributes(): void
    {
        $model = new Model();
        /** @psalm-var Rule[][] $rules */
        $rules = $model->getRulesWithAttributes();
        $this->assertIsArray($rules);
        $this->assertInstanceOf(Required::class, $rules['login'][0]);
        $this->assertInstanceOf(HasLength::class, $rules['login'][1]);
        $this->assertInstanceOf(Email::class, $rules['login'][2]);
        $this->assertInstanceOf(Required::class, $rules['password'][0]);
        $this->assertInstanceOf(HasLength::class, $rules['password'][1]);
    }

    public function testHas(): void
    {
        $model = new Model();
        $this->assertTrue($model->has('login'));
        $this->assertTrue($model->has('password'));
    }

    public function testIsEmpty(): void
    {
        $model = new Model();
        $this->assertTrue($model->isEmpty());
    }

    public function testLoad(): void
    {
        $model = new Model();
        $this->assertTrue($model->load(['Model' => ['login' => 'test', 'password' => 'test']]));
        $this->assertSame('test', $model->getAttributeValue('login'));
        $this->assertSame('test', $model->getAttributeValue('password'));
    }

    public function testLoadPublicField(): void
    {
        $model = new Model();
        $this->assertEmpty($model->public);

        $data = [
            'Model' => [
                'public' => 'samdark',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertSame('samdark', $model->public);
    }

    public function testLoadWithEmptyScope(): void
    {
        $model = new class () extends AbstractModel {
            private int $int = 1;
            private string $string = 'string';
            private float $float = 3.14;
            private bool $bool = true;
        };
        $model->load([
            'int' => '2',
            'float' => '3.15',
            'bool' => 'false',
            'string' => 555,
        ], '');
        $this->assertIsInt($model->getCastValue('int'));
        $this->assertIsFloat($model->getCastValue('float'));
        $this->assertIsBool($model->getCastValue('bool'));
        $this->assertIsString($model->getCastValue('string'));
    }

    public function testSet(): void
    {
        $model = new Model();
        $model->set('login', 'test');
        $model->set('password', 'test');
        $this->assertSame('test', $model->getAttributeValue('login'));
        $this->assertSame('test', $model->getAttributeValue('password'));
    }

    public function testValidateInvalid(): void
    {
        $model = new Model();
        $model->set('login', '@example.com');
        $model->set('password', '7');
        $this->assertFalse($model->validate());
        $this->assertSame(
            ['login' => ['This value is not a valid email address.'], 'password' => ['Is too short.']],
            $model->error()->getAll()
        );
    }

    public function testValidateValid(): void
    {
        $model = new Model();
        $model->set('login', 'admin@example.com');
        $model->set('password', 'Pol98767');
        $this->assertTrue($model->validate());
        $this->assertSame([], $model->error()->getAll());
    }

    public function testProtectedCollectAttributes(): void
    {
        $model = new class () extends AbstractModel {
            protected int $int = 1;

            public function collectAttributes(): array
            {
                return array_merge(parent::collectAttributes(), ['null' => 'null']);
            }
        };
        $this->assertSame(['int' => 'int', 'null' => 'null'], $model->collectAttributes());
    }

    public function testSetFormErrors(): void
    {
        $formErrors = new CustomFormErrors();
        $model = new Model();
        $model->setFormErrors($formErrors);
        $this->assertSame($formErrors, $model->error());
    }

    public function testSetValidator(): void
    {
        $validator = new Validator();
        $model = new Model();
        $model->setValidator($validator);
        $this->assertSame($validator, $model->validator());
    }

    public function testSetValidatorValidate(): void
    {
        $validator = new Validator();
        $model = new Rules();
        $model->setValidator($validator);
        $model->set('firstName', 'joe');
        $model->set('lastName', 'doe');
        $this->assertTrue($model->validate());
        $this->assertSame([], $model->error()->getAll());
    }
}
