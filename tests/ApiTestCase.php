<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Traits\Asserts;

class ApiTestCase extends TestCase
{
    use Asserts,
        DatabaseTransactions;
}
