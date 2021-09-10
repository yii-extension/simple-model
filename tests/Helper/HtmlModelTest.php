<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Simple\Model\BaseModel;
use Yii\Extension\Simple\Model\Helper\HtmlModel;
use Yii\Extension\Simple\Model\ModelInterface;
use Yii\Extension\Simple\Model\Tests\Stub\LoginModelStub;

final class HtmlModelTest extends TestCase
{
    public function testFirstError(): void
    {
        $model = new LoginModelStub();
        $model->addError('password', 'Invalid password.');
        $this->assertTrue($model->hasErrors());
        $this->assertEquals('Invalid password.', HtmlModel::getFirstError($model, 'password'));
    }

    public function testGetAttributeHint(): void
    {
        $model = new LoginModelStub();
        $this->assertSame('Write your id or email.', HtmlModel::getAttributeHint($model, 'login'));
    }


    public function testGetAttributeLabel(): void
    {
        $model = new LoginModelStub();
        $this->assertSame('Login:', HtmlModel::getAttributeLabel($model, 'login'));
    }

    public function testGetAttributeValue(): void
    {
        $model = new LoginModelStub();
        $this->assertSame(null, HtmlModel::getAttributeValue($model, 'login'));
    }

    public function testGetAttributeName(): void
    {
        $model = new LoginModelStub();
        $this->assertSame('login', HtmlModel::getAttributeName($model, '[0]login'));
        $this->assertSame('login', HtmlModel::getAttributeName($model, 'login[0]'));
        $this->assertSame('login', HtmlModel::getAttributeName($model, '[0]login[0]'));
    }

    public function testGetAttributeNameException(): void
    {
        $model = new LoginModelStub();

        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        HtmlModel::getAttributeName($model, 'noExist');
    }

    public function testGetAttributeNameInvalid(): void
    {
        $model = new LoginModelStub();
        $this->expectExceptionMessage('Attribute name must contain word characters only.');
        HtmlModel::getAttributeName($model, 'content body');
    }

    public function testGetInputId(): void
    {
        $model = new LoginModelStub();
        $this->assertSame('loginmodel-login', HtmlModel::getInputId($model, 'login'));

    }

    public function dataGetInputName(): array
    {
        $LoginModelStub = new LoginModelStub();
        return [
            [$LoginModelStub, '[0]content', 'LoginModel[0][content]'],
            [$LoginModelStub, 'dates[0]', 'LoginModel[dates][0]'],
            [$LoginModelStub, '[0]dates[0]', 'LoginModel[0][dates][0]'],
            [$LoginModelStub, 'age', 'LoginModel[age]'],
        ];
    }

    /**
     * @dataProvider dataGetInputName
     *
     * @param ModelInterface $form
     * @param string $attribute
     * @param string $expected
     */
    public function testGetInputName(ModelInterface $form, string $attribute, string $expected): void
    {
        $this->assertSame($expected, HtmlModel::getInputName($form, $attribute));
    }
}
