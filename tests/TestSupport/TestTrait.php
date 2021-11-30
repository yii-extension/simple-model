<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\TestSupport;

use ReflectionException;
use ReflectionObject;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\Validator;

trait TestTrait
{
    protected function createValidator(): Validator
    {
        return new Validator(new Formatter());
    }

    /**
     * Invokes a inaccessible method.
     *
     * @param object $object
     * @param string $method
     * @param array $args
     * @param bool $revoke whether to make method inaccessible after execution.
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    protected function invokeMethod(object $object, string $method, array $args = [], bool $revoke = true): mixed
    {
        $reflection = new ReflectionObject($object);

        $method = $reflection->getMethod($method);

        $method->setAccessible(true);

        $result = $method->invokeArgs($object, $args);

        if ($revoke) {
            $method->setAccessible(false);
        }

        return $result;
    }
}
