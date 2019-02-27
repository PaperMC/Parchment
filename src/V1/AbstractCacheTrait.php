<?php

namespace App\V1;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait AbstractCacheTrait {
    /**
     * @param ParameterBagInterface $bag
     * @return CacheItemPoolInterface
     */
    protected function getCache(ParameterBagInterface $bag) {
        $directory = $bag->get('parchment.cache');
        $adapter = new FilesystemAdapter('parchment', 0, $directory);
        return $adapter;
    }

    /**
     * @param ParameterBagInterface $bag
     * @param string $project
     * @param string $extra
     * @return string
     */
    protected function getDownloadsPath(ParameterBagInterface $bag, string $project, string $extra = '') {
        return $bag->get('parchment.downloads') . '/' . $project . '/' . $extra;
    }
}
