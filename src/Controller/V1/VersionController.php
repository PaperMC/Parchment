<?php

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VersionController extends AbstractController {
    use V1ControllerTrait;

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
            'builds' => [
                'latest' => static::getLatestBuild($builds),
                'all' => $builds
            ]
        ]);
    }
}
