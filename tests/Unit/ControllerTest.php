<?php

namespace Tests\Unit;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Controller;
use App\Core\Exceptions\ValidationExceptions;

class ControllerTest extends TestCase
{
    use Controller;
    private Controller $controller;

    protected function setUp(): void
    {
        $this->controller = new Controller();
    }

    public function testRender()
    {
        $view = 'home';
        $data = ['name' => 'John Doe'];

        ob_start();
        $this->controller->render($view, $data);
        $output = ob_get_clean();

        $expectedOutput = file_get_contents(base_path("resources/views/$view.view.php"));
        $expectedOutput = str_replace('{{ name }}', 'John Doe', $expectedOutput);

        $this->assertEquals($expectedOutput, $output);
    }

    public function testValidate()
    {
        $old = ['name' => 'John Doe', 'email' => 'john.doe@example.com'];
        $rules = ['name' => 'required', 'email' => 'required|email'];

        $this->controller->validate($old, $rules);

        $this->assertEmpty($this->controller->errors);
    }

    public function testValidateWithErrors()
    {
        $old = ['name' => '', 'email' => 'invalid email'];
        $rules = ['name' => 'required', 'email' => 'required|email'];

        $this->controller->validate($old, $rules);

        $this->assertNotEmpty($this->controller->errors);
        $this->assertArrayHasKey('name', $this->controller->errors);
        $this->assertArrayHasKey('email', $this->controller->errors);
    }

    public function testFailed()
    {
        $this->controller->errors = ['name' => 'This field is required'];

        $this->assertTrue($this->controller->failed());
    }

    public function testError()
    {
        $this->controller->error('name', 'This field is required');

        $this->assertArrayHasKey('name', $this->controller->errors);
        $this->assertEquals('This field is required', $this->controller->errors['name']);
    }

    public function testThrowValidationException()
    {
        $old = ['name' => '', 'email' => 'invalid email'];
        $rules = ['name' => 'required', 'email' => 'required|email'];

        $this->expectException(ValidationExceptions::class);

        $this->controller->validate($old, $rules);
        $this->controller->throwValidationException();
    }

    public function testValidateCSRFToken()
    {
        $request = new Request();
        $request->setMethod('POST');
        $request->setInput('csrf_token', 'invalid-token');

        Session::put('csrf_token', 'valid-token');

        $this->expectException(Response::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);

        $this->controller->validateCSRFToken();
    }
}
