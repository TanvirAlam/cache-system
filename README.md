# Simple Caching System

# Installation

Step-1: Clone the repo
Step-2: `git clone https://github.com/Laradock/laradock.git` install docker
Step-3: Get into docker directory: run `sudo docker-compose up -d nginx`
Step-3: run `composer-dump autoload`
Step-4: run `composer install or update`
Step-5: `http://localhost/`

Run the Test:

- Get in to `cache-system` directories and run `vendor/bin/phpunit tests/`

# Packaged used

    -   `"psr/simple-cache": "^1.0@dev"`:
        - Standered interface for caching libraries. It holds all interfaces defined by PSR-16. Note that this is NOT a Cache implementation of its own. It is merely an interface that describes a Cache implementation. link: https://github.com/php-fig/simple-cache

    -   `"doctrine/cache": "^1.6"`:
        -   Doctrine provides cache drivers in the Common package for some of the most popular caching implementations such as APC, Memcache and Xcache. We also provide an ArrayCache driver which stores the data in a PHP array. Obviously, when using ArrayCache, the cache does not persist between requests, but this is useful for testing in a development environment.

    -   `"phpunit/phpunit": "^7.0"`:
        -   PHPUnit is a programmer-oriented testing framework for PHP. It is an instance of the xUnit architecture for unit testing frameworks.

    -   `"squizlabs/php_codesniffer": "^3.1"`:
        -   PHP_CodeSniffer is a set of two PHP scripts; the main phpcs script that tokenizes PHP, JavaScript and CSS files to detect violations of a defined coding standard, and a second phpcbf script to automatically correct coding standard violations. PHP_CodeSniffer is an essential development tool that ensures your code remains clean and consistent.

    -   `"php": "^7.1.3"`

# Objective:

    This basically utilize the cache drivers to save data to a cache, check if some cached data exists, fetch the cached data and delete the cached data. I am using `ArrayCache` implementation.

    Key Words:

    `protected $key, $value, $isHit, $timeToLive, $handler;`

    key: name of the cache key

    value: arry type value of the cache

    isHit: A cache hit occurs when requesting an `Item` by key and a matching value is found for that key, and that value has not expired, and the value is not invalid for some other reason

    isMiss: A cache miss is the opposite of a cache hit

    timeToLive: experiation time, the actual time when an item is set to be removed

    handler: Different drivers: `APC`, `APCu`, `Memcache`, etc


    We have a single class `SimpleCache`, it implements `CacheInterface` from 'psr/simple-cache'.
        Reading Cache: { KeyName }
        ```
            $this->cache->get('key');

        ```
        Writing Cache: { KeyName, Value }
        ```
            $this->cache->set('key', 'foobar');

        ```
         Deleting Cache: { KeyName }
        ```
            $this->cache->delete('key');

        ```
        Deleting multipel items Cache: { KeyName1, ... , KeyName~N }
        ```
            $this->cache->deleteMultiple('key1', 'key1', 'key1' ...);

        ```

    - Can store PHP data types:
    ```
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

    ```
