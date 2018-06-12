<?php
use Src\Item;

class ItemTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function create_null_value()
    {
        $item = new Item('my_key');
        $this->assertNull($item->get());
    }

    /** @test */
    public function new_item_is_cache_miss()
    {
        $item = new Item('my_key');
        $this->assertFalse($item->isHit());
    }

    /** @test */
    public function cache_is_set_as_string()
    {
        $item = new Item('my_key');
        $item->set('value');
        $this->assertSame('value', $item->get());
    }

    /** @test */
    public function cache_is_set_as_int()
    {
        $item = new Item('my_key');
        $item->set(1);
        $this->assertSame(1, $item->get());
    }

    /** @test */
    public function cache_is_set_as_array()
    {
        $item = new Item('my_key');
        $item->set([1 => 'hello', 'world' => 2]);
        $this->assertSame([1 => 'hello', 'world' => 2], $item->get());
    }

    /** @test */
    public function cache_is_set_as_object()
    {
        $item = new Item('my_key');
        $item->set(new stdClass());
        $this->assertEquals(new stdClass(), $item->get());
    }

    /** @test */
    public function cache_is_set_to_expires_after_10_sec()
    {
        $item = new Item('my_key');
        $item->set(new stdClass());
        $item->expiresAfter(10);
        $this->assertTrue($item->isHit());
    }

    /** @test */
    public function cache_is_set_to_expires_after_date()
    {
        $item = new Item('my_key');
        $item->set(new stdClass());
        $oneDay = DateInterval::createFromDateString('1 day');
        $item->expiresAfter($oneDay);
        $this->assertTrue($item->isHit());
    }

    /** @test */
    public function cache_expires_at_is_hit_tomorrow()
    {
        $item = new Item('my_key');
        $item->set(new stdClass());
        $tomorrow = (new DateTime('now'))->add(DateInterval::createFromDateString('1 day'));
        $item->expiresAt($tomorrow);
        $this->assertTrue($item->isHit());
    }

    /** @test */
    public function cache_expires_at_yesterday_is_not_hit()
    {
        $item = new Item('my_key');
        $item->set(new stdClass());
        $yesterday = (new DateTime('now'))->add(DateInterval::createFromDateString('-1 day'));
        $item->expiresAt($yesterday);
        $this->assertFalse($item->isHit());
    }

    /** @test */
    public function cache_is_set_to_expires_after_is_hit()
    {
        $item = new Item('my_key');
        $item->set(new stdClass());
        $item->expiresAfter(60);
        $this->assertTrue($item->isHit());
    }

    /** @test */
    public function cache_is_set_to_expires_after_is_not_hit()
    {
        $item = new Item('my_key');
        $item->set(new stdClass());
        $item->expiresAfter(0);
        $this->assertFalse($item->isHit());
    }
}