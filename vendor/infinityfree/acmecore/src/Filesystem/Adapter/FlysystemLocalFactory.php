<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Filesystem\Adapter;

use InfinityFree\AcmeCore\Filesystem\FilesystemFactoryInterface;
use InfinityFree\AcmeCore\Filesystem\FilesystemInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Webmozart\Assert\Assert;

class FlysystemLocalFactory implements FilesystemFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $config): FilesystemInterface
    {
        Assert::keyExists($config, 'root', 'create::$config expected an array with the key %s.');

        return new FlysystemAdapter(new Filesystem(new Local($config['root'])));
    }
}
