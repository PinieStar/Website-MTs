<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tes tidak boleh bergantung pada Vite dev server atau hasil build produksi.
        $this->withoutVite();
    }
}
