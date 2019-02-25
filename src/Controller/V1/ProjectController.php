<?php

namespace App\Controller\V1;

use Symfony\Component\Finder\Finder;

class ProjectController extends AbstractV1Controller {
    public function index($project) {
        $versions = $this->getVersions($project);
        return $this->json([
            'project' => $project,
            'versions' => $versions
        ]);
    }

    private function getVersions($project) {
        $cache = $this->getCache();

        $versions = $cache->get(static::makeVersionCacheKey($project));

        if ($versions === null) {
            $dir = $this->getParameter('parchment.downloads') . '/' . $project . '/';
            if(!file_exists($dir)) {
                throw $this->createNotFoundException();
            }

            $finder = new Finder();
            $finder->directories()->in($this->getParameter('parchment.downloads') . '/' . $project . '/');

            $versions = [];
            foreach ($finder as $file) {
                $versions[] = $file->getBasename();
            }

            rsort($versions, SORT_NATURAL);

            $cache->set(static::makeVersionCacheKey($project), $versions);
        }

        return $versions;
    }
}
