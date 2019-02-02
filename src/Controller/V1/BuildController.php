<?php

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildController extends AbstractController {
    public function index($project, $version, $build) {
        return $this->json([
            'project' => $project,
            'version' => $version,
            'build' => $build
        ]);
    }

    public function download($project, $version, $build) {
        $filePath = $this->getParameter('parchment.downloads') . '/' . $project . '/' . $version . '/' . $build . '.jar';
        return $this->file($filePath, $project . '-' . $build . '.jar');
    }
}
