<?php

namespace App\Controller\V1;

use App\Access\ParameterBagAccessTrait;
use App\V1\AbstractCacheTrait;
use App\V1\BuildCacheTrait;
use App\V1\VersionCacheTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VersionController extends AbstractController {
    use AbstractCacheTrait;
    use BuildCacheTrait;
    use ParameterBagAccessTrait;
    use VersionCacheTrait;
    use V1ControllerTrait;

    public function index($project, $version) {
        if(!$this->hasVersion($this->getParameterBag(), $project, $version)) {
            throw $this->createNotFoundException('Could not locate version');
        }

        $builds = $this->getVersions($this->getParameterBag(), $project);
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
