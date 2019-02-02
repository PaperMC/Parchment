<?php

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Finder\Finder;

class VersionController extends AbstractController {
    public function index($project, $version) {
        $builds = $this->getBuilds($project, $version);
        return $this->json([
            'project' => $project,
            'version' => $version,
            'builds' => $builds
        ]);
    }

    private function getBuilds($project, $version) {
        $cacheDir = $this->getParameter('parchment.cache');
        $cache = new FilesystemCache('parchment', 0, $cacheDir);

        $builds = $cache->get($this->makeCacheKey($project, $version));

        if ($builds === null) {
            $finder = new Finder();
            $finder->files()->in($this->getParameter('parchment.downloads') . '/' . $project . '/' .  $version);

            $builds = [];
            foreach ($finder as $file) {
                $builds[] = $file->getBasename('.jar');
            }

            rsort($builds, SORT_NATURAL);

            $cache->set($this->makeCacheKey($project, $version), $builds);
        }

        return [
            'latest' => $builds[0],
            'all' => $builds
        ];
    }

    private function makeCacheKey($project, $version) {
        return $project . '.builds.' . $version;
    }
}
