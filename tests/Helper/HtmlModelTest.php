<?php

declare(strict_types=1);



use PHPUnit\Framework\TestCase;
use Yii\Extension\Simple\Model\BaseModel;
use Yii\Extension\Simple\Model\Helper\HtmlModel;
use Yii\Extension\Simple\Model\ModelInterface;
use Yii\Extension\Simple\Model\Tests\Model\LoginModel;

final class HtmlModelTest extends TestCase
{
    public function testGetAttributeHint(): void
    {
        $model = new LoginModel();
        $this->assertSame('Write your id or email.', HtmlModel::getAttributeHint($model, 'login'));

        $anonymousForm = new class () extends BaseModel {
            private string $age = '';
        };
        $this->assertEmpty(HtmlModel::getAttributeHint($anonymousForm, 'age'));
    }

    public function testGetAttributeName(): void
    {
        $model = new LoginModel();
        $this->assertSame('login', HtmlModel::getAttributeName($model, '[0]login'));
        $this->assertSame('login', HtmlModel::getAttributeName($model, 'login[0]'));
        $this->assertSame('login', HtmlModel::getAttributeName($model, '[0]login[0]'));
    }

    public function testGetAttributeNameException(): void
    {
        $model = new LoginModel();
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        HtmlModel::getAttributeName($model, 'noExist');
    }

    public function testGetAttributeNameInvalid(): void
    {
        $model = new LoginModel();
        $this->expectExceptionMessage('Attribute name must contain word characters only.');
        HtmlModel::getAttributeName($model, 'content body');
    }

    public function dataGetInputName(): array
    {
        $loginModel = new LoginModel();
        $anonymousModel = new class () extends BaseModel {
        };

        return [
            [$loginModel, '[0]content', 'LoginModel[0][content]'],
            [$loginModel, 'dates[0]', 'LoginModel[dates][0]'],
            [$loginModel, '[0]dates[0]', 'LoginModel[0][dates][0]'],
            [$loginModel, 'age', 'LoginModel[age]'],
            [$anonymousModel, 'dates[0]', 'dates[0]'],
            [$anonymousModel, 'age', 'age'],
        ];
    }

    /**
     * @dataProvider dataGetInputName
     *
     * @param ModelInterface $model
     * @param string $attribute
     * @param string $expected
     */
    public function testGetInputName(ModelInterface $model, string $attribute, string $expected): void
    {
        $this->assertSame($expected, HtmlModel::getInputName($model, $attribute));
    }

    public function testGetInputNameException(): void
    {
        $anonymousForm = new class () extends BaseModel {
        };

        $this->expectExceptionMessage('formName() cannot be empty for tabular inputs.');
        HtmlModel::getInputName($anonymousForm, '[0]dates[0]');
    }
}
