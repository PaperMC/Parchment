<?php

namespace App\Controller\V1;

use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Finder\Finder;

trait V1ControllerTrait {
    protected function getCache() {
        $directory = $this->getParameter('parchment.cache');
        $cache = new FilesystemCache('parchment', 0, $directory);
        return $cache;
    }

    protected function getDownloadsPath($project, $extra = '') {
        return $this->getParameter('parchment.downloads') . '/' . $project . '/' . $extra;
    }

    protected function getVersions($project) {
        $cache = $this->getCache();

        $versions = $cache->get(static::makeVersionCacheKey($project));

        if ($versions === null) {
            $dir = $this->getDownloadsPath($project);
            if(!file_exists($dir)) {
                throw $this->createNotFoundException();
            }

            $finder = new Finder();
            $finder->directories()->in($dir);

            $versions = [];
            foreach ($finder as $file) {
                $versions[] = $file->getBasename();
            }

            rsort($versions, SORT_NATURAL);

            $cache->set(static::makeVersionCacheKey($project), $versions);
        }

        return $versions;
    }

    protected static function makeVersionCacheKey($project) {
        return $project . '.versions';
    }

    protected function hasBuild($project, $version, $build) {
        $builds = $this->getBuilds($project, $version);
        return in_array($build, $builds);
    }

    protected function getBuilds($project, $version) {
        $cache = $this->getCache();
        $builds = $cache->get(static::makeBuildCacheKey($project, $version));
        if ($builds === null) {
            $finder = new Finder();
            $finder->files()->in($this->getDownloadsPath($project , $version));

            $builds = [];
            foreach ($finder as $file) {
                $builds[] = $file->getBasename('.jar');
            }

            rsort($builds, SORT_NATURAL);

            $cache->set(static::makeBuildCacheKey($project, $version), $builds);
        }

        return $builds;
    }

    protected static function getLatestBuild($builds) {
        return $builds[0];
    }

    protected static function makeBuildCacheKey($project, $version) {
        return $project . '.builds.' . $version;
    }
}
