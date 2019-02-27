<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\V1;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

trait BuildCacheTrait {
    /**
     * @param ParameterBagInterface $bag
     * @param string $project
     * @param string $version
     * @param string $build
     * @return bool
     */
    protected function hasBuild(ParameterBagInterface $bag, string $project, string $version, string $build) {
        $builds = $this->getBuilds($bag, $project, $version);
        return in_array($build, $builds);
    }

    protected function addBuild(ParameterBagInterface $bag, CacheItemPoolInterface $cache, string $project, string $version, string $build) {
        $builds = $this->getBuilds($bag, $project, $version);
        $builds[] = $build;
        rsort($builds, SORT_NATURAL);
        $item = $cache->getItem(static::makeBuildCacheKey($project, $version));
        $item->set($builds);
        $cache->save($item);
        return $builds;
    }

    protected function getBuilds(ParameterBagInterface $bag, string $project, string $version) {
        $cache = $this->getCache($bag);
        $builds = $this->findAndCacheBuilds($bag, $cache, $project, $version);
        return $builds;
    }

    protected function findAndCacheBuilds(ParameterBagInterface $bag, CacheItemPoolInterface $cache, string $project, string $version) {
        $builds = $this->findBuilds($bag, $project, $version);
        if(!empty($builds)) {
            $item = $cache->getItem(static::makeBuildCacheKey($project, $version));
            $item->set($builds);
            $cache->save($item);
        }
        return $builds;
    }

    protected function findBuilds(ParameterBagInterface $bag, string $project, string $version) {
        $directory = $this->getDownloadsPath($bag, $project , $version);
        if(!file_exists($directory)) {
            return [];
        }

        $finder = new Finder();
        $finder->files()->in($directory);

        $builds = [];
        foreach ($finder as $file) {
            $builds[] = $file->getBasename('.jar');
        }

        rsort($builds, SORT_NATURAL);

        return $builds;
    }

    protected static function makeBuildCacheKey(string $project, string $version) {
        return $project . '.builds.' . $version;
    }
}
