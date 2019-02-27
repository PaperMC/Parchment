<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Access;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

trait ParameterBagAccessTrait {
    protected function getParameterBag() {
        if (!$this->container->has('parameter_bag')) {
            throw new ServiceNotFoundException('parameter_bag', null, null, array(), sprintf('The "%s::getBag()" method is missing a parameter bag to work properly.', \get_class($this)));
        }

        return $this->container->get('parameter_bag');
    }
}
