<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\V1;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

trait BuildCacheTrait {
    use AbstractCacheTrait;

    protected function hasBuild(ParameterBagInterface $bag, string $project, string $version, string $build) {
        $builds = $this->getBuilds($bag, $project, $version);
        return in_array($build, $builds);
    }

    protected function addBuild(ParameterBagInterface $bag, CacheInterface $cache, string $project, string $version, string $build) {
        $builds = $this->getBuilds($bag, $project, $version);
        $builds[] = $build;
        rsort($builds, SORT_NATURAL);
        $cache->set(static::makeBuildCacheKey($project, $version), $builds);
        return $builds;
    }

    protected function getBuilds(ParameterBagInterface $bag, string $project, string $version) {
        $cache = $this->getCache($bag);

        $builds = $cache->get(static::makeBuildCacheKey($project, $version));
        if ($builds === null) {
            $builds = $this->findBuilds($bag, $project, $version);
            $cache->set(static::makeBuildCacheKey($project, $version), $builds);
        }

        return $builds;
    }

    protected function findBuilds(ParameterBagInterface $bag, string $project, string $version) {
        $finder = new Finder();
        $finder->files()->in($this->getDownloadsPath($bag, $project , $version));

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
