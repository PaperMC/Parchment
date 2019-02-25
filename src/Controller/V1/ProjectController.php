<?php

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController {
    use V1ControllerTrait;

    public function index($project) {
        $versions = $this->getVersions($project);
        return $this->json([
            'project' => $project,
            'versions' => $versions
        ]);
    }
}
