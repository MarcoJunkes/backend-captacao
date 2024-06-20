<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\iso4217Controller;
use Illuminate\Http\Request;
use App\Repository\iso4217Repository;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class iso4217ControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if store method returns error when no code or number is provided.
     */
    public function test_store_method_returns_error_on_empty_input()
    {
        $repositoryMock = Mockery::mock(iso4217Repository::class);

        // Substitui a instância do repository no container do Laravel
        $this->app->instance(iso4217Repository::class, $repositoryMock);

        $controller = $this->app->make(iso4217Controller::class);

        $request = Request::create('/iso4217/store', 'POST', []);

        $response = $controller->store($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Nenhum código ou número fornecido.', $responseData['error']);
    }

    public function test_coin_list()
    {
        $repositoryMock = Mockery::mock(iso4217Repository::class);

        // Define expectativa do mock
        $repositoryMock->shouldReceive('updateOrCreate')->once()->andReturn(true);

        $this->app->instance(iso4217Repository::class, $repositoryMock);

        $controller = $this->app->make(iso4217Controller::class);

        $request = Request::create('/iso4217/store', 'POST', [
            'code' => 'BRL'
        ]);

        $response = $controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        $this->assertIsArray($responseData);
    }

    public function test_invalid_code()
    {
        $repositoryMock = Mockery::mock(iso4217Repository::class);

        $this->app->instance(iso4217Repository::class, $repositoryMock);

        $controller = $this->app->make(iso4217Controller::class);

        $request = Request::create('/iso4217/store', 'POST', [
            'code' => 'ZZZ'
        ]);

        $response = $controller->store($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Código ou número fornecido inválido ou inexistente', $responseData['error']);
    }

    protected function tearDown(): void
    {
        // Limpa Mockery
        Mockery::close();
        parent::tearDown();
    }
}
