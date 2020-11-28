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
            throw $this->createNotFoundException('Could not locate build');
        }

        $hash = $this->getBuildHash($this->getParameterBag(), $project, $version, $build);

        $response =  $this->json([
            'project' => $project,
            'version' => $version,
            'build' => $build,
            'hash' => $hash
        ]);
        $response->setPublic();
        $response->setSharedMaxAge(604800); # 7 days
        return $response;
    }

    public function download($project, $version, $build) {
        $bag = $this->getParameterBag();
        if(!$this->hasBuild($bag, $project, $version, $build)) {
            throw $this->createNotFoundException('Could not locate build');
        }
        $filePath = $this->getDownloadsPath($bag, $project, $version . '/' . $build . '.jar');
        return $this->jarFile($filePath, $project . '-' . $build . '.jar');
    }

    public function latest($project, $version) {
        $builds = $this->getBuilds($this->getParameterBag(), $project, $version);
        if(empty($builds)) {
            throw $this->createNotFoundException('Could not locate latest build');
        }
        $build = static::getLatestBuild($builds);
        $response = $this->build($project, $version, $build);
        # Override max age response for latest
        $response->setSharedMaxAge(120); # 2 minutes
        return $response;
    }

    public function latestDownload($project, $version) {
        $bag = $this->getParameterBag();
        $builds = $this->getBuilds($bag, $project, $version);
        if(empty($builds)) {
            throw $this->createNotFoundException('Could not locate latest build');
        }
        $build = static::getLatestBuild($builds);
        $response = $this->download($project, $version, $build);
        # Override max age response for latest
        $response->setSharedMaxAge(120); # 2 minutes
        return $response;
    }

    private function jarFile($file, $fileName) {
        $response = $this->file($file, $fileName);
        $response->headers->set('Content-Type', 'application/java-archive');
        $response->setAutoLastModified();
        $response->setPublic();
        $response->setSharedMaxAge(604800); # 7 days
        return $response;
    }
}
