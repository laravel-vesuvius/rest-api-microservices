<?php

declare(strict_types=1);

namespace App\Tests;

use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{
    use TransactionalTrait;

    /**
     * @var KernelBrowser|null
     */
    protected $client;

    /**
     * @var Generator|null
     */
    protected static $faker;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::$faker = Factory::create();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient([
            'environment' => 'test',
            'debug' => true,
        ]);
        //$this->client->disableReboot();

        self::$faker = Factory::create();

        $this->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->rollbackTransaction();

        self::$faker = null;

        parent::tearDown();
    }
}
