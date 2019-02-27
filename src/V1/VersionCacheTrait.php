<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\V1;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

trait VersionCacheTrait {
    protected function hasVersion(ParameterBagInterface $bag, string $project, string $version) {
        $versions = $this->getVersions($bag, $project);
        return in_array($version, $versions);
    }

    protected function addVersion(ParameterBagInterface $bag, CacheItemPoolInterface $cache, string $project, string $version) {
        $versions = $this->getVersions($bag, $project);
        $versions[] = $version;
        rsort($versions, SORT_NATURAL);
        $item = $cache->getItem(static::makeVersionCacheKey($project));
        $item->set($versions);
        $cache->save($item);
        return $versions;
    }

    protected function getVersions(ParameterBagInterface $bag, string $project) {
        $cache = $this->getCache($bag);
        $versions = $this->findAndCacheVersions($bag, $cache, $project);
        return $versions;
    }

    protected function findAndCacheVersions(ParameterBagInterface $bag, CacheItemPoolInterface $cache, string $project) {
        $versions = $this->findVersions($bag, $project);
        $item = $cache->getItem(static::makeVersionCacheKey($project));
        $item->set($versions);
        $cache->save($item);
        return $versions;
    }

    protected function findVersions(ParameterBagInterface $bag, string $project) {
        $directory = $this->getDownloadsPath($bag, $project);
        if(!file_exists($directory)) {
            return [];
        }

        $finder = new Finder();
        $finder->directories()->in($directory);

        $versions = [];
        foreach ($finder as $file) {
            $versions[] = $file->getBasename();
        }

        rsort($versions, SORT_NATURAL);

        return $versions;
    }

    protected static function makeVersionCacheKey(string $project) {
        return $project . '.versions';
    }
}
