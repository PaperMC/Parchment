<?php

namespace App\Controller\V1;

class BuildController extends AbstractV1Controller {
    public function index($project, $version, $build) {
        if(!$this->hasBuild($project, $version, $build)) {
            throw $this->createNotFoundException();
        }

        return $this->json([
            'project' => $project,
            'version' => $version,
            'build' => $build
        ]);
    }

    public function download($project, $version, $build) {
        if(!$this->hasBuild($project, $version, $build)) {
            throw $this->createNotFoundException();
        }

        $filePath = $this->getParameter('parchment.downloads') . '/' . $project . '/' . $version . '/' . $build . '.jar';
        return $this->file($filePath, $project . '-' . $build . '.jar');
    }

    private function hasBuild($project, $version, $build) {
        $cache = $this->getCache();
        $builds = $cache->get(static::makeBuildCacheKey($project, $version));
        return in_array($build, $builds);
    }
}
