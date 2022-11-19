<?php

namespace Tests\Api;

use App\Http\Controllers\ApiController;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase;

class ApiControllerTest extends ApiTestCase
{
    /** @test */
    public function get_http_status_code_returns_the_status_code()
    {
        $apiController = new ApiController;

        $this->assertEquals(
            200,
            $apiController->getHTTPStatusCode()
        );

        $apiController->setHTTPStatusCode(300);

        $this->assertEquals(
            300,
            $apiController->getHTTPStatusCode()
        );
    }

    /** @test */
    public function get_error_code_returns_the_error_code()
    {
        $apiController = new ApiController;

        $this->assertNull(
            $apiController->getErrorCode()
        );

        $apiController->setErrorCode(30);

        $this->assertEquals(
            30,
            $apiController->getErrorCode()
        );
    }

    /** @test */
    public function get_limit_per_page_code_returns_the_limit_per_page()
    {
        $apiController = new ApiController;

        $this->assertEquals(
            10,
            $apiController->getLimitPerPage()
        );

        $apiController->setLimitPerPage(30);

        $this->assertEquals(
            30,
            $apiController->getLimitPerPage()
        );
    }

    /** @test */
    public function it_gets_the_sort_criteria()
    {
        $apiController = new ApiController;

        $this->assertEquals(
            'created_at',
            $apiController->getSortCriteria()
        );

        $apiController->setSortCriteria('created_at');

        $this->assertEquals(
            'created_at',
            $apiController->getSortCriteria()
        );
    }

    /** @test */
    public function it_only_accepts_some_sorting_parameters()
    {
        $apiController = new ApiController;

        $apiController->setSortCriteria('created_at');

        $this->assertEquals(
            'created_at',
            $apiController->getSortCriteria()
        );

        $apiController->setSortCriteria('anything');

        $this->assertEquals(
            '',
            $apiController->getSortCriteria()
        );
    }

    /** @test */
    public function calling_api_with_insane_limit_number_raises_an_error()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);

        $insaneLimit = config('api.max_limit_per_page') * 100;

        $response = $this->json('GET', "/api/users?limit={$insaneLimit}");

        $response->assertStatus(400);

        $response->assertJsonFragment([
            'message' => 'The limit parameter is too big',
            'error_code' => 30,
        ]);
    }

    /** @test */
    public function calling_api_with_a_wrong_sort_parameter_raises_an_error()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user, ['read']);

        $criteria = 'anything';

        $response = $this->json('GET', "/api/users?sort={$criteria}");

        $response->assertStatus(400);

        $response->assertJsonFragment([
            'message' => 'The sorting criteria is invalid',
            'error_code' => 39,
        ]);
    }
}
