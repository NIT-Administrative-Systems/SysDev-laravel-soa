<?php

namespace Northwestern\SysDev\SOA\Tests\Concerns;

trait TestsOpenAM11
{
    private function ssoResponseJson(string $netid = 'dog1234', bool $isDuoAuthed = false): string
    {
        return json_encode([
            'username' => $netid,
            'universalId' => sprintf('id=%s,ou=user,ou=am-config,dc=northwestern,dc=edu', $netid),
            'realm' => '/',
            'latestAccessTime' => '2019-11-18T14:17:03Z',
            'maxIdleExpirationTime' => '2019-11-18T18:17:03Z',
            'maxSessionExpirationTime' => '2019-11-19T02:17:02Z',
            'properties' => [
                'AMCtxId' => 'faa9b02e-df32-4c3b-9775-9c1e310d7265-91217',
                'isDuoAuthenticated' => $isDuoAuthed,
            ],
        ]);
    }
}
