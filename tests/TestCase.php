<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests;

use ReflectionObject;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\Validator;

use function str_replace;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function createValidator(): Validator
    {
        return new Validator(new Formatter());
    }

    /**
     * Asserting two strings equality ignoring line endings.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    protected function assertEqualsWithoutLE(string $expected, string $actual, string $message = ''): void
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Invokes a inaccessible method.
     *
     * @param object $object
     * @param string $method
     * @param array $args
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    protected function invokeMethod(object $object, string $method, array $args = [])
    {
        $reflection = new ReflectionObject($object);

        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        $result = $method->invokeArgs($object, $args);
        $method->setAccessible(false);
        return $result;
    }
}
