<?php

namespace App\Controller\V1;

use App\Access\ParameterBagAccessTrait;
use App\V1\AbstractCacheTrait;
use App\V1\VersionCacheTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController {
    use AbstractCacheTrait;
    use ParameterBagAccessTrait;
    use VersionCacheTrait;

    public function index($project) {
        $versions = $this->getVersions($this->getParameterBag(), $project);
        if($versions == null) {
            throw $this->createNotFoundException('Could not locate project');
        }

        $response = $this->json([
            'project' => $project,
            'versions' => $versions
        ]);
        $response->setPublic();
        $response->setSharedMaxAge(1800); # 30 minutes
        return $response;
    }
}
