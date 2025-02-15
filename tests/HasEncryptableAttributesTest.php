<?php

namespace Alemian95\Tests;

use Alemian95\Tests\Models\FakeUser;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Config\Repository as Config;

class HasEncryptableAttributesTest extends TestCase
{
    protected function setUp(): void
    {

        parent::setUp();

        // Init Laravel application
        $app = new Container();
        Container::setInstance($app);
        Facade::setFacadeApplication($app);

        // Register config
        $config = new Config([
            'app' => [
                'key' => 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=',
                'cipher' => 'AES-256-CBC',
            ],
        ]);
        $app->instance('config', $config);

        // Register encrypter service
        $app->bind('encrypter', function ($app) {
            return new \Illuminate\Encryption\Encrypter(
                base64_decode('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA='), 
                'AES-256-CBC'
            );
        });

        // Register encryption facade
        (new EncryptionServiceProvider($app))->register();
        Crypt::swap($app['encrypter']);

    }

    public function testEncryptsAttributeWhenSet()
    {
        $user = new FakeUser();
        $user->secret = 'secret_value';

        $this->assertNotEquals('secret_value', $user->getAttributes()['secret']);
        $this->assertEquals('secret_value', Crypt::decrypt($user->getAttributes()['secret']));
    }

    public function testDecryptsAttributeWhenAccessed()
    {
        $user = new FakeUser();
        $user->secret = 'secret_value';

        $this->assertEquals('secret_value', $user->secret);
    }
}
