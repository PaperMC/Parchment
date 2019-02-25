<?php

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Simple\FilesystemCache;

class AbstractV1Controller extends AbstractController {
    protected function getCache() {
        $directory = $this->getParameter('parchment.cache');
        $cache = new FilesystemCache('parchment', 0, $directory);
        return $cache;
    }

    protected static function makeBuildCacheKey($project, $version) {
        return $project . '.builds.' . $version;
    }

    protected static function makeVersionCacheKey($project) {
        return $project . '.versions';
    }
}
