<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Laravel 12.69.x kadang tidak mengenali --drop-views / --drop-types
     * pada command migrate:fresh di environment tertentu.
     */
    protected function migrateFreshUsing()
    {
        return array_merge(
            [
                '--seed'       => $this->shouldSeed(),
                '--seeder'     => $this->seeder(),
            ],
        );
    }
}