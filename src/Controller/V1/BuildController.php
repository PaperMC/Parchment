<?php

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildController extends AbstractController {
    use V1ControllerTrait;

    public function build($project, $version, $build) {
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
        $filePath = $this->getDownloadsPath($project, $version . '/' . $build . '.jar');
        return $this->file($filePath, $project . '-' . $build . '.jar');
    }

    public function latest($project, $version) {
        $builds = $this->getBuilds($project, $version);
        if(empty($builds)) {
            throw $this->createNotFoundException();
        }
        $build = static::getLatestBuild($builds);
        return $this->build($project, $version, $build);
    }

    public function latestDownload($project, $version) {
        $builds = $this->getBuilds($project, $version);
        if(empty($builds)) {
            throw $this->createNotFoundException();
        }
        $build = static::getLatestBuild($builds);
        return $this->download($project, $version, $build);
    }
}
