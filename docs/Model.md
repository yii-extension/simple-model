# Model

Models are part of the MVC architecture. They are objects representing business data, rules and logic.

You can create model classes by extending `\Yii\Extension\Model\Model`, supports many useful features:

- Attributes: represents the properties of the model, they are defined by using at least one modifier (such as `Visibility`, `TypeHint`) and a name.
- Errors: represents the errors of the model instance, a collection of methods to add and get errors for the specified attribute, or all attributes.
- Types: represents the types of the model attributes, they are defined by using at least one modifier (`TypeHint`), for example `string`, `int`, `float`, `bool`, `array`, `object`, `iterable`, `null`.
- Validation: ensures input data based on the declared validation rules;

## Usage in YiiFramework v.3

Create simple model class:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Yii\Extension\Model\Model;

final class MyModel extends AbstractModel
{
    private ?string $login = null;
    private ?string $password = null;
}
```

In controller:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\MyModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class MyController
{
    public function action(ServerRequestInterface $serverRequest, ViewRenderer $viewRenderer): ResponseInterface
    {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $model = new MyModel();

        if ($method === 'POST' && $model->load($body)) {
            // ... do something
        }

        return $viewRenderer->render('registration/register', ['body' => $body, 'model' => $model]);
    }
}
```

Create simple model class with validation rules:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Yii\Extension\Model\Model;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class MyModel extends AbstractModel
{
    private ?string $login = null;
    private ?string $password = null;

    public function getRules(): array
    {
        return [
            'login' => [
                new Required(),
                new HasLength(min: 4, max: 40, tooShortMessage: 'Is too short.', tooLongMessage: 'Is too long.'),
                new Email(),
            ],
            'password' => [
                new Required(),
                new HasLength(min: 8, tooShortMessage: 'Is too short.'),
            ],
        ];
    }
}
```

In controller:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\MyModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class MyController
{
    public function action(ServerRequestInterface $serverRequest, ViewRenderer $viewRenderer): ResponseInterface
    {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $model = new MyModel();

        if ($method === 'POST' && $model->load($body) && $model->validate()) {
            // ... do something
        }

        return $viewRenderer->render('registration/register', ['body' => $body, 'model' => $model]);
    }
}
```

Create simple model class with validation rules with attributes `PHP 8.0`:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Yii\Extension\Model\Model;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class MyModel extends AbstractModel
{
    #[Required]
    #[HasLength(min: 4, max: 40, tooShortMessage: 'Is too short.', tooLongMessage: 'Is too long.')]
    #[Email]    
    private ?string $login = null;

    #[Required]
    #[HasLength(min: 8, tooShortMessage: 'Is too short.')]
    private ?string $password = null;
}
```

In controller:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\MyModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class MyController
{
    public function action(ServerRequestInterface $serverRequest, ViewRenderer $viewRenderer): ResponseInterface
    {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $model = new MyModel();

        if ($method === 'POST' && $model->load($body) && $model->validate()) {
            // ... do something
        }

        return $viewRenderer->render('registration/register', ['body' => $body, 'model' => $model]);
    }
}
```

## Reference

Method | Description |
-------|-------------|
`attributes()` | returns the list names of attributes.
`errors()` | returns instance of [ModelErrors::class](/docs/ModelErrors.md).
`getAttributeValue(string $attribute)` | returns the attribute value.
`getCastValue(string $attribute)` | returns the attribute `PHP` type cast value.
`getFormName()` | returns the form name.
`getRules()` | returns the validation rules.
`getRulesWithAttributes` | returns the validation rules with `PHP` attributes.
`has(string $attribute)` | returns whether the attribute exists.
`isEmpty()` | returns whether the model instance is empty.
`load(array $data, ?string $formName = null)` | loads the model with the given data.
`setFormErrors(ModelErrorsContract $ModelErrors)` | sets custom class for `ModelErrors::class`.
`setValidator(ValidatorInterface $validator)` | sets custom class for `ValidatorInterface::class`.
`setValue(string $name, mixed $value)` | sets the attribute value.
`setValues(array $values)` | sets the attribute values for model instance.
`types()` | returns instance of [ModelTypes::class](/docs/ModelTypes.md).
`validate()` | validates the model instance.
`validator()` | returns instance of `ValidatorInterface::class`.
