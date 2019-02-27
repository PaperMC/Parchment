<?php

namespace App\Controller\V1;

trait V1ControllerTrait {
    protected static function getLatestBuild($builds) {
        return $builds[0];
    }
}
