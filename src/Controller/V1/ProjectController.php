<?php

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Finder\Finder;

class ProjectController extends AbstractController {
    public function index($project) {
        $versions = $this->getVersions($project);
        return $this->json([
            'project' => $project,
            'versions' => $versions
        ]);
    }

    private function getVersions($project) {
        $cacheDir = $this->getParameter('parchment.cache');
        $cache = new FilesystemCache('parchment', 0, $cacheDir);

        $versions = $cache->get($this->makeCacheKey($project));

        if ($versions === null) {
            $finder = new Finder();
            $finder->directories()->in($this->getParameter('parchment.downloads') . '/' . $project . '/');

            $versions = [];
            foreach ($finder as $file) {
                $versions[] = $file->getBasename();
            }

            rsort($versions, SORT_NATURAL);

            $cache->set($this->makeCacheKey($project), $versions);
        }

        return $versions;
    }

    private function makeCacheKey($project) {
        return $project . '.versions';
    }
}
