<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Model;

use Yii\Extension\Simple\Model\BaseModel;

final class NestedAttributeModel extends BaseModel
{
    private ?int $id = null;
    private ?LoginModel $user = null;
    private ?StubModel $stubModel = null;

    public function __construct()
    {
        $this->user = new LoginModel();
        $this->stubModel = new StubModel();

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
            'id' => new Required(),
        ];
    }

    public function setUserLogin(string $login): void
    {
        $this->user->login('admin');
    }
}
