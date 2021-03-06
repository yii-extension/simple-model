<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\Attribute\FormModelAttributes;
use Yii\Extension\Model\Tests\TestSupport\FormModel\Dynamic;

final class DinamicModelTest extends TestCase
{
    public function dynamicAttributesProvider(): array
    {
        return [
            [
                [
                    [
                        'name' => '7aeceb9b-fa64-4a83-ae6a-5f602772c01b',
                        'value' => 'some uuid value',
                        'expected' => 'Dynamic[7aeceb9b-fa64-4a83-ae6a-5f602772c01b]',
                    ],
                    [
                        'name' => 'test_field',
                        'value' => 'some test value',
                        'expected' => 'Dynamic[test_field]',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dynamicAttributesProvider
     */
    public function testUUIDInputName(array $fields): void
    {
        $keys = array_column($fields, 'name');
        $form = new Dynamic(array_fill_keys($keys, null));

        /** @psalm-var string[][] $fields */
        foreach ($fields as $field) {
            $inputName = FormModelAttributes::getInputName($form, $field['name']);
            $this->assertSame($field['expected'], $inputName);
            $this->assertTrue($form->has($field['name']));
            $this->assertNull($form->getValue($field['name']));

            $form->setValue($field['name'], $field['value']);
            $this->assertSame($field['value'], $form->getValue($field['name']));
        }
    }
}
