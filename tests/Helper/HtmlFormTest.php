<?php

declare(strict_types=1);

namespace Yii\Extension\Simple\Model\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yii\Extension\Simple\Model\FormModel;
use Yii\Extension\Simple\Model\FormModelInterface;
use Yii\Extension\Simple\Model\Helper\HtmlForm;
use Yii\Extension\Simple\Model\Tests\FormModel\Login;

final class HtmlFormTest extends TestCase
{
    public function testGetAttributeHint(): void
    {
        $model = new Login();
        $this->assertSame('Write your id or email.', HtmlForm::getAttributeHint($model, 'login'));

        $anonymousForm = new class () extends FormModel {
            private string $age = '';
        };
        $this->assertEmpty(HtmlForm::getAttributeHint($anonymousForm, 'age'));
    }

    public function testGetAttributeLabel(): void
    {
        $model = new Login();
        $this->assertSame('Login:', HtmlForm::getAttributeLabel($model, 'login'));
    }

    public function testGetAttributeName(): void
    {
        $model = new Login();
        $this->assertSame('login', HtmlForm::getAttributeName($model, '[0]login'));
        $this->assertSame('login', HtmlForm::getAttributeName($model, 'login[0]'));
        $this->assertSame('login', HtmlForm::getAttributeName($model, '[0]login[0]'));
    }

    public function testGetAttributeNameException(): void
    {
        $model = new Login();
        $this->expectExceptionMessage("Attribute 'noExist' does not exist.");
        HtmlForm::getAttributeName($model, 'noExist');
    }

    public function testGetAttributeNameInvalid(): void
    {
        $model = new Login();
        $this->expectExceptionMessage('Attribute name must contain word characters only.');
        HtmlForm::getAttributeName($model, 'content body');
    }

    public function dataGetInputName(): array
    {
        $loginModel = new Login();
        $anonymousModel = new class () extends FormModel {
        };

        return [
            [$loginModel, '[0]content', 'Login[0][content]'],
            [$loginModel, 'dates[0]', 'Login[dates][0]'],
            [$loginModel, '[0]dates[0]', 'Login[0][dates][0]'],
            [$loginModel, 'age', 'Login[age]'],
            [$anonymousModel, 'dates[0]', 'dates[0]'],
            [$anonymousModel, 'age', 'age'],
        ];
    }

    public function testGetAttributeValue(): void
    {
        $model = new Login();
        $this->assertNull(HtmlForm::getAttributeValue($model, 'login'));
    }

    public function testGetInputId(): void
    {
        $model = new Login();
        $this->assertSame('login-login', HtmlForm::getInputId($model, 'login'));
    }

    /**
     * @dataProvider dataGetInputName
     *
     * @param FormModelInterface $model
     * @param string $attribute
     * @param string $expected
     */
    public function testGetInputName(FormModelInterface $model, string $attribute, string $expected): void
    {
        $this->assertSame($expected, HtmlForm::getInputName($model, $attribute));
    }

    public function testGetInputNameException(): void
    {
        $anonymousForm = new class () extends FormModel {
        };

        $this->expectExceptionMessage('formName() cannot be empty for tabular inputs.');
        HtmlForm::getInputName($anonymousForm, '[0]dates[0]');
    }
}
