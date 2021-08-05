<?php

namespace Tests;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JansenFelipe\FakerBR\FakerBR;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    /**
     * @var Factory
     */
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create('pt_BR');
        $this->faker->addProvider(new FakerBR($this->faker));
    }
}
