<?php

namespace Northwestern\SysDev\SOA\Tests\Auth\Entity;

use Northwestern\SysDev\SOA\Auth\Entity\ActiveDirectoryUser;
use Orchestra\Testbench\TestCase;

class ActiveDirectoryUserTest extends TestCase
{
    public function testEntity()
    {
        $user = new ActiveDirectoryUser([
            'mailNickname' => 'TEST123',
            'mail' => 'foo@bar.net',
            'userPrincipalName' => 'TEST123@foo.bar.net',
            'displayName' => 'Foo Bar',
            'givenName' => 'Foo',
            'surname' => 'Bar',
        ]);

        $this->assertEquals('test123', $user->getNetid());
        $this->assertEquals('foo@bar.net', $user->getEmail());
        $this->assertEquals('TEST123@foo.bar.net', $user->getUserPrincipalName());
        $this->assertEquals('Foo Bar', $user->getDisplayName());
        $this->assertEquals('Foo', $user->getFirstName());
        $this->assertEquals('Bar', $user->getLastName());

    }
}
