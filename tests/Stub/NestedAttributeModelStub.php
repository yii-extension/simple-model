<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Stub;

use Yii\Extension\Simple\Model\AbstractModel;

final class NestedAttributeModelStub extends AbstractModel
{
    private ?int $id = null;
    private ?LoginModelStub $user = null;
    private ?StubClass $stubClass = null;

    public function __construct()
    {
        $this->user = new LoginModelStub();
        $this->stubClass = new StubClass();

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
