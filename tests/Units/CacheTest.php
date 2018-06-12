<?php

namespace Tests\Units;

use DateInterval;
use DateTime;
use Doctrine\Common\Cache\ArrayCache;
use Src\SimpleCache;
use Src\Interfaces\CacheInterface;

class CacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return CacheInterface
     */
    protected function newCache()
    {
        return new SimpleCache(new ArrayCache(), 'key', null, false);
    }

    public function setUp()
    {
        parent::setUp();

        $this->cache = $this->newCache();
    }

    /** @test */
    public function remove_any_existing_cache()
    {
        $this->cache->clear();
        $value = $this->cache->get('key');
        $this->assertSame(null, $value);
    }

    /** @test */
    public function check_if_string_can_set_cache()
    {
        $this->cache->set('key', 'foobar');
        $value = $this->cache->get('key');
        $this->assertEquals('foobar', $value);
    }

    /** @test */
    public function check_if_integer_can_set_cache()
    {
        $this->cache->set('key', 123123);
        $value = $this->cache->get('key');
        $this->assertEquals(123123, $value);
    }

    /** @test */
    public function check_if_floats_can_set_cache()
    {
        $this->cache->set('key', 123.123);
        $value = $this->cache->get('key');
        $this->assertEquals(123.123, $value);
    }

    /** @test */
    public function check_if_boolean_can_set_cache()
    {
        $this->cache->set('key', false);
        $value = $this->cache->get('key');
        $this->assertEquals(false, $value);
    }

    /** @test */
    public function check_if_null_can_set_cache()
    {
        $this->cache->set('key', null);
        $value = $this->cache->get('key');
        $this->assertEquals(null, $value);
    }

    /** @test */
    public function check_if_array_can_set_cache()
    {
        $this->cache->set('key', [1, 2, 3, 4]);
        $value = $this->cache->get('key');
        $this->assertEquals([1, 2, 3, 4], $value);
    }

    /** @test */
    public function check_if_object_can_set_cache()
    {
        $this->cache->set('key', (object)['property' => 'Here we go']);
        $value = $this->cache->get('key');
        $this->assertEquals((object)['property' => 'Here we go'], $value);
    }

    /** @test */
    public function remove_cache_single_item()
    {
        $this->cache->delete('key');
        $value = $this->cache->get('key');
        $this->assertSame(null, $value);
    }

    /** @test */
    public function remove_existing_cache()
    {
        $this->cache->clear();
        $value = $this->cache->getMultiple(['key', 'foo']);
        $this->assertSame(['key' => null, 'foo' => null], $value);
    }

    /** @test */
    public function check_if_multpel_load_calls_return_same_result()
    {
        $this->cache->setMultiple(['key' => 'foo', 'foo' => 'bar']);
        $value = $this->cache->getMultiple(['key', 'foo']);
        $this->assertSame(['key' => 'foo', 'foo' => 'bar'], $value);
    }

    /** @test */
    public function remove_cache_multipel_item()
    {
        $this->cache->deleteMultiple(['key', 'foo']);
        $value = $this->cache->getMultiple(['key', 'foo']);
        $this->assertSame(['key' => null, 'foo' => null], $value);
    }

    /** @test */
    public function expires_at_return_integers()
    {
        $expires = time() + 2;
        $this->assertEquals(0, $this->cache->getTimeToLive());
        $this->cache->expiresAt($expires);
        $this->assertEquals(2, $this->cache->getTimeToLive());
    }

    /** @test */
    public function expires_at_return_date()
    {
        $expires = new DateTime('+2 seconds');
        $this->assertEquals(0, $this->cache->getTimeToLive());
        $this->cache->expiresAt($expires);
        $this->assertEquals(2, $this->cache->getTimeToLive());
    }

    /** @test */
    public function expires_at_return_null()
    {
        $this->assertEquals(0, $this->cache->getTimeToLive());
        $this->cache->expiresAt(null);
        $this->assertEquals(0, $this->cache->getTimeToLive());
    }

    /** @test */
    public function expires_at_return_invalid_type()
    {
        $this->assertEquals(0, $this->cache->getTimeToLive());
        $this->expectException(\Exception::class);
        $this->cache->expiresAt('foo');
    }

    /** @test */
    public function expires_after_return_integer()
    {
        $expires = 2;
        $this->assertEquals(0, $this->cache->getTimeToLive());
        $this->cache->expiresAfter($expires);
        $this->assertEquals(2, $this->cache->getTimeToLive());
    }

    /** @test */
    public function expires_after_return_date()
    {
        $expires = new DateInterval('PT2S');
        $this->assertEquals(0, $this->cache->getTimeToLive());
        $this->cache->expiresAfter($expires);
        $this->assertEquals(2, $this->cache->getTimeToLive());
    }

    /** @test */
    public function expires_after_return_null()
    {
        $this->assertEquals(0, $this->cache->getTimeToLive());
        $this->cache->expiresAfter(null);
        $this->assertEquals(0, $this->cache->getTimeToLive());
    }

    /** @test */
    public function expires_after_return_invalid_type()
    {
        $this->expectException(\Exception::class);
        $this->cache->expiresAfter('foo');
    }
}
