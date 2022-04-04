<?php

declare(strict_types=1);

namespace Yiisoft\Form\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yii\Extension\FormModel\Attribute\FormModelAttributes;
use Yii\Extension\FormModel\Tests\TestSupport\FormModel\Dynamic;

final class HtmlFormTest extends TestCase
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

        foreach ($fields as $field) {
            $inputName = FormModelAttributes::getInputName($form, $field['name']);
            $this->assertSame($field['expected'], $inputName);
            $this->assertTrue($form->has($field['name']));
            $this->assertNull($form->getValue($field['name']));

            $form->set($field['name'], $field['value']);
            $this->assertSame($field['value'], $form->getValue($field['name']));
        }
    }
}
