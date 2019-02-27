<?php

namespace App\Controller\V1;

use App\Access\ParameterBagAccessTrait;
use App\V1\AbstractCacheTrait;
use App\V1\BuildCacheTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildController extends AbstractController {
    use AbstractCacheTrait;
    use BuildCacheTrait;
    use ParameterBagAccessTrait;
    use V1ControllerTrait;

    public function build($project, $version, $build) {
        if(!$this->hasBuild($this->getParameterBag(), $project, $version, $build)) {
            throw $this->createNotFoundException();
        }
        return $this->json([
            'project' => $project,
            'version' => $version,
            'build' => $build
        ]);
    }

    public function download($project, $version, $build) {
        $bag = $this->getParameterBag();
        if(!$this->hasBuild($bag, $project, $version, $build)) {
            throw $this->createNotFoundException();
        }
        $filePath = $this->getDownloadsPath($bag, $project, $version . '/' . $build . '.jar');
        return $this->file($filePath, $project . '-' . $build . '.jar');
    }

    public function latest($project, $version) {
        $builds = $this->getBuilds($this->getParameterBag(), $project, $version);
        if(empty($builds)) {
            throw $this->createNotFoundException();
        }
        $build = static::getLatestBuild($builds);
        return $this->build($project, $version, $build);
    }

    public function latestDownload($project, $version) {
        $bag = $this->getParameterBag();
        $builds = $this->getBuilds($bag, $project, $version);
        if(empty($builds)) {
            throw $this->createNotFoundException();
        }
        $build = static::getLatestBuild($builds);
        return $this->download($project, $version, $build);
    }
}
