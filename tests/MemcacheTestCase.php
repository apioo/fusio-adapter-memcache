<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Memcache\Tests;

use Fusio\Adapter\Memcache\Connection\Memcache;
use Fusio\Engine\Model\Connection;
use Fusio\Engine\Parameters;
use Fusio\Engine\Test\CallbackConnection;
use Fusio\Engine\Test\EngineTestCaseTrait;

/**
 * MemcacheTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
abstract class MemcacheTestCase extends \PHPUnit_Framework_TestCase
{
    use EngineTestCaseTrait;

    protected static $hasConnection = true;

    /**
     * @var \Memcached
     */
    protected $connection;

    protected function setUp()
    {
        if (!self::$hasConnection) {
            $this->markTestSkipped('Memcache connection not available');
        }

        if (!$this->connection) {
            $this->connection = $this->newConnection();
        }

        $connection = new Connection();
        $connection->setId(1);
        $connection->setName('foo');
        $connection->setClass(CallbackConnection::class);
        $connection->setConfig([
            'callback' => function(){
                return $this->connection;
            },
        ]);

        $this->getConnectionRepository()->add($connection);
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (self::$hasConnection) {
            if ($this->connection instanceof \Memcached) {
                $this->connection->flush();
            }
        }
    }

    protected function newConnection()
    {
        $connector = new Memcache();

        try {
            $connection = $connector->getConnection(new Parameters([
                'server' => '127.0.0.1:11211',
            ]));

            return $connection;
        } catch (\Exception $e) {
            self::$hasConnection = false;

            $this->markTestSkipped('Memcache connection not available');
        }

        return null;
    }
}
