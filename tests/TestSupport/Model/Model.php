<?php

declare(strict_types=1);

namespace Yii\Extension\Model\Tests\TestSupport\Model;

use Yii\Extension\Model\Model as AbstractModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

final class Model extends AbstractModel
{
    public string $public = '';

    #[Required]
    #[HasLength(min: 4, max: 40, tooShortMessage: 'Is too short.', tooLongMessage: 'Is too long.')]
    #[Email]
    private ?string $login = null;

    #[Required]
    #[HasLength(min: 8, tooShortMessage: 'Is too short.')]
    private ?string $password = null;
}
