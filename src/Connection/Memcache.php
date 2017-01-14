<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2016 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Memcache\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;

/**
 * Memcache
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Memcache implements ConnectionInterface
{
    public function getName()
    {
        return 'Memcache';
    }

    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Memcached
     */
    public function getConnection(ParametersInterface $config)
    {
        if (class_exists('Memcached')) {
            $memcache = new \Memcached();
            $parts    = explode(',', $config->get('server'));

            foreach ($parts as $part) {
                $part = trim($part);
                $pos  = strrpos($part, ':');
                if ($pos !== false) {
                    $ip   = substr($part, 0, $pos);
                    $port = (int) substr($part, $pos + 1);
                } else {
                    $ip   = $part;
                    $port = 11211;
                }

                $memcache->addServer($ip, $port);
            }

            return $memcache;
        } else {
            throw new ConfigurationException('PHP extension "memcached" is not installed');
        }
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
        $builder->add($elementFactory->newInput('server', 'Server', 'text', 'Comma seperated list of [ip]:[port] i.e. <code>192.168.2.18:11211,192.168.2.19:11211</code>'));
    }
}
