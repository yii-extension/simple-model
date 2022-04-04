<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\FormModel;

use Yii\Extension\Model\FormModel;
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

    public function getLabels(): array
    {
        return [
            'id' => 'Id',
        ];
    }

    public function getHints(): array
    {
        return [
            'id' => 'Readonly ID',
        ];
    }

    public function getRules(): array
    {
        return [
            'id' => Required::rule(),
        ];
    }
}
