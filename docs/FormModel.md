# FormModel

FormModel is the base class, that allows us to handle the methods used in forms, such as `Hint`, `Label`, `Placeholder`.

## Usage in YiiFramework v.3

Create a new class, that extends `FormModel::class`.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Yii\Extension\Model\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class Login extends FormModel
{
    private ?string $login = null;
    private ?string $password = null;
    private bool $rememberMe = false;

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRememberMe(): bool
    {
        return $this->rememberMe;
    }

    public function login(string $value): void
    {
        $this->login = $value;
    }

    public function password(string $value): void
    {
        $this->password = $value;
    }

    public function rememberMe(bool $value): void
    {
        $this->rememberMe = $value;
    }

    public function getFormName(): string
    {
        return 'Login';
    }

    public function getHints(): array
    {
        return [
            'login' => 'Write your id or email.',
            'password' => 'Write your password.',
        ];
    }

    public function getLabels(): array
    {
        return [
            'login' => 'Login:',
            'password' => 'Password:',
            'rememberMe' => 'remember Me:',
        ];
    }

    public function getPlaceholders(): array
    {
        return [
            'login' => 'Write Username or Email.',
            'password' => 'Write Password.',
        ];
    }

    public function getRules(): array
    {
        return [
            'login' => $this->loginRules(),
            'password' => $this->passwordRules(),
        ];
    }

    private function loginRules(): array
    {
        return [
            new Required(),
            new HasLength(min: 4, max: 40, tooShortMessage: 'Is too short.', tooLongMessage: 'Is too long.'),
            new Email(),
        ];
    }

    private function passwordRules(): array
    {
        return [
            new Required(),
            new HasLength(min: 8, tooShortMessage: 'Is too short.'),
        ];
    }
}
```

In controller:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Login;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class MyController
{
    public function action(ServerRequestInterface $serverRequest, ViewRenderer $viewRenderer): ResponseInterface
    {
        $body = $serverRequest->getParsedBody();
        $method = $serverRequest->getMethod();
        $model = new Login();

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
`getHint(string $attribute)` | returns the hint for the attribute.
`getHints()` | returns the hints for all attributes.
`getLabel(string $attribute)` | returns the label for the attribute.
`getLabels()` | returns the labels for all attributes.
`getPlaceholder(string $attribute)` | returns the placeholder for the attribute.
`getPlaceholders()` | returns the placeholders for all attributes.
