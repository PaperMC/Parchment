<?php

namespace App\V1;

use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait AbstractCacheTrait {
    protected function getCache(ParameterBagInterface $bag) {
        $directory = $bag->get('parchment.cache');
        $cache = new FilesystemCache('parchment', 0, $directory);
        return $cache;
    }

    protected function getDownloadsPath(ParameterBagInterface $bag, string $project, string $extra = '') {
        return $bag->get('parchment.downloads') . '/' . $project . '/' . $extra;
    }
}
