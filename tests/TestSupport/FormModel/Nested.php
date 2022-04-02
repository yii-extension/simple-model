<?php

declare(strict_types=1);

namespace Yii\Extension\FormModel\Tests\TestSupport\FormModel;

use Yii\Extension\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;

final class Nested extends FormModel
{
    private ?int $id = null;
    private Login $user;

    public function __construct()
    {
        $this->user = new Login();

        parent::__construct();
    }

    public function getAttributeLabels(): array
    {
        return [
            'id' => 'Id',
        ];
    }

    public function getAttributeHints(): array
    {
        return [
            'id' => 'Readonly ID',
        ];
    }

    public function getLastName(): string
    {
        return $this->user->getLastName();
    }

    public function getName(): string
    {
        return $this->user->name;
    }

    public function getUserLogin(): ?string
    {
        return $this->user->getLogin();
    }

    public function getRules(): array
    {
        return [
            'id' => Required::rule(),
        ];
    }

    public function setUserLogin(string $login): void
    {
        $this->user->login($login);
    }
}
