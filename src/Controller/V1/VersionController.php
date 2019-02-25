<?php

namespace App\Controller\V1;

use Symfony\Component\Finder\Finder;

class VersionController extends AbstractV1Controller {
    public function index($project, $version) {
        $cache = $this->getCache();
        $versions = $cache->get(static::makeVersionCacheKey($project));
        if(!in_array($version, $versions)) {
            throw $this->createNotFoundException();
        }

        $builds = $this->getBuilds($project, $version);
        return $this->json([
            'project' => $project,
            'version' => $version,
            'builds' => $builds
        ]);
    }

    private function getBuilds($project, $version) {
        $cache = $this->getCache();

        $builds = $cache->get(static::makeBuildCacheKey($project, $version));

        if ($builds === null) {
            $finder = new Finder();
            $finder->files()->in($this->getParameter('parchment.downloads') . '/' . $project . '/' .  $version);

            $builds = [];
            foreach ($finder as $file) {
                $builds[] = $file->getBasename('.jar');
            }

            rsort($builds, SORT_NATURAL);

            $cache->set(static::makeBuildCacheKey($project, $version), $builds);
        }

        return [
            'latest' => $builds[0],
            'all' => $builds
        ];
    }
}
