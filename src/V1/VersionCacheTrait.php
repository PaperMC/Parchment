<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\V1;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

trait VersionCacheTrait {
    protected function hasVersion(ParameterBagInterface $bag, string $project, string $version) {
        $versions = $this->getVersions($bag, $project);
        return in_array($version, $versions);
    }

    protected function addVersion(ParameterBagInterface $bag, CacheInterface $cache, string $project, string $version) {
        $versions = $this->getVersions($bag, $project);
        $versions[] = $version;
        rsort($versions, SORT_NATURAL);
        $cache->set(static::makeVersionCacheKey($project), $versions);
        return $versions;
    }

    protected function getVersions(ParameterBagInterface $bag, string $project) {
        $cache = $this->getCache($bag);

        $versions = $cache->get(static::makeVersionCacheKey($project));
        if ($versions === null) {
            $versions = $this->findAndCacheVersions($bag, $cache, $project);
        }

        return $versions;
    }

    protected function findAndCacheVersions(ParameterBagInterface $bag, CacheInterface $cache, string $project) {
        $versions = $this->findVersions($bag, $project);
        $cache->set(static::makeVersionCacheKey($project), $versions);
        return $versions;
    }

    protected function findVersions(ParameterBagInterface $bag, string $project) {
        $dir = $this->getDownloadsPath($bag, $project);
        if(!file_exists($dir)) {
            return [];
        }

        $finder = new Finder();
        $finder->directories()->in($dir);

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
