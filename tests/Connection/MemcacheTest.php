<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Memcache\Tests\Connection;

use Fusio\Adapter\Memcache\Connection\Memcache;
use Fusio\Adapter\Memcache\Tests\MemcacheTestCase;
use Fusio\Engine\Form\Builder;
use Fusio\Engine\Form\Container;
use Fusio\Engine\Form\Element\Collection;
use Fusio\Engine\Parameters;

/**
 * MemcacheTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class MemcacheTest extends MemcacheTestCase
{
    public function testGetConnection()
    {
        $connectionFactory = $this->getConnectionFactory()->factory(Memcache::class);

        $config = new Parameters([
            'host' => ['127.0.0.1:11211'],
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertInstanceOf(\Memcache::class, $connection);

        $connection->add('foo', 'bar');

        $this->assertEquals('bar', $connection->get('foo'));
    }

    public function testConfigure()
    {
        $connection = $this->getConnectionFactory()->factory(Memcache::class);
        $builder    = new Builder();
        $factory    = $this->getFormElementFactory();

        $connection->configure($builder, $factory);

        $this->assertInstanceOf(Container::class, $builder->getForm());

        $elements = $builder->getForm()->getElements();
        $this->assertEquals(1, count($elements));
        $this->assertInstanceOf(Collection::class, $elements[0]);
    }

    public function testPing()
    {
        $connectionFactory = $this->getConnectionFactory()->factory(Memcache::class);

        $config = new Parameters([
            'host' => ['127.0.0.1:11211'],
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertTrue($connectionFactory->ping($connection));
    }
}
