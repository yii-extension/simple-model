<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Model\FormValidator;
use Yii\Extension\Model\Tests\TestSupport\TestTrait;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ScalarDataSet;
use Yiisoft\Validator\DataSetInterface;

final class FormValidatorTest extends TestCase
{
    use TestTrait;

    public function testnormalizeDataSet(): void
    {
        $validator = new FormValidator();
        $arrayDataSet = $this->invokeMethod($validator, 'normalizeDataSet', [['foo' => 'bar', 'baz' => 'qux']]);
        $this->assertInstanceOf(ArrayDataSet::class, $arrayDataSet);
        $this->assertInstanceOf(DataSetInterface::class, $arrayDataSet);

        $scalarDataSet = $this->invokeMethod($validator, 'normalizeDataSet', [1]);
        $this->assertInstanceOf(ScalarDataSet::class, $scalarDataSet);
        $this->assertInstanceOf(DataSetInterface::class, $arrayDataSet);
    }
}
