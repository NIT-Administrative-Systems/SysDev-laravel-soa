<?php

namespace Northwestern\SysDev\SOA\Tests\Feature\Auth\OAuth2\Key;

use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Northwestern\SysDev\SOA\Auth\OAuth2\Key\KeyToConstraintAdapter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Orchestra\Testbench\TestCase;

#[CoversClass(KeyToConstraintAdapter::class)]
class KeyToConstraintAdapterTest extends TestCase
{
    private const JWKS_URL = 'http://northwestern.edu';

    #[DataProvider('keysetProvider')]
    public function test_key_parsing(string $jwksJsonFilename, ?int $validConstraints, int $invalidKeys): void
    {
        $adapter = $this->adapter();

        // Inject the fixture into the cache so the class loads it.
        $cacheKey = invade($adapter)->keysetCacheKey(self::JWKS_URL);

        $json = file_get_contents(__DIR__."/fixtures/$jwksJsonFilename");
        $json = json_decode($json, true);
        Cache::put($cacheKey, $json);

        $keysetContainer = $adapter->configToConstraints(self::JWKS_URL);

        $actualConstraintCount = null;
        if ($keysetContainer->constraints) {
            $actualConstraintCount = count(invade($keysetContainer->constraints)->constraints);
        }

        $this->assertEquals($validConstraints, $actualConstraintCount);
        $this->assertCount($invalidKeys, $keysetContainer->failedKeys);
    }

    public function test_empty_keyset(): void
    {
        $adapter = $this->adapter();

        // Inject the fixture into the cache so the class loads it.
        $cacheKey = invade($adapter)->keysetCacheKey(self::JWKS_URL);

        $json = file_get_contents(__DIR__."/fixtures/empty.json");
        $json = json_decode($json, true);
        Cache::put($cacheKey, $json);

        $this->expectException(InvalidArgumentException::class);
        $adapter->configToConstraints(self::JWKS_URL);
    }


    public static function keysetProvider(): array
    {
        return [
            // JSON, expected constraint count (or null), expected error count
            'seven' => ['seven-valid.json', 7, 0],
            'mixed-support' => ['mixed-support.json', 1, 1],
            'none-supported' => ['all-unsupported.json', null, 1],
        ];
    }

    private function adapter(): KeyToConstraintAdapter
    {
        return new KeyToConstraintAdapter();
    }
}