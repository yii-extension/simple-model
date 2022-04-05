<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Tests\TestSupport\FormModel\PropertyType;

final class ModelTypeTest extends TestCase
{
    public function testGetAttributes(): void
    {
        $model = new PropertyType();
        $this->assertSame(
            ['array' => 'array', 'bool' => 'bool', 'float' => 'float', 'int' => 'int', 'object' => 'object', 'string' => 'string' ],
            $model->getTypes()->getAttributes()
        );
    }

    public function testPhpTypeCast(): void
    {
        $model = new PropertyType();
        $this->assertSame('1.1', $model->getTypes()->phpTypeCast('string', 1.1));
        $this->assertSame(1.1, $model->getTypes()->phpTypeCast('float', '1.1'));
    }

    public function testPropertyStringable(): void
    {
        $model = new PropertyType();
        $objectStringable = new class () {
            public function __toString(): string
            {
                return 'joe doe';
            }
        };

        $model->setValue('string', $objectStringable);
        $this->assertSame('joe doe', $model->getAttributeValue('string'));
    }

    public function testSetValue(): void
    {
        $model = new PropertyType();

        // value is array
        $model->setValue('array', []);
        $this->assertSame([], $model->getAttributeValue('array'));

        // value is string
        $model->setValue('string', 'string');
        $this->assertSame('string', $model->getAttributeValue('string'));

        // value is int
        $model->setvalue('int', 1);
        $this->assertSame(1, $model->getAttributeValue('int'));

        // value is bool
        $model->setValue('bool', true);
        $this->assertSame(true, $model->getAttributeValue('bool'));

        // value is null
        $model->setValue('object', null);
        $this->assertNull($model->getAttributeValue('object'));
    }

    public function testSetValueException(): void
    {
        $model = new PropertyType();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value is not of type "string".');
        $model->setValue('string', []);
    }
}
