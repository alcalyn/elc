<?php

namespace EL\Bundle\Game\CheckersBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use EL\Bundle\CoreBundle\AbstractGame\Bundle;

class GameCheckersBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass($this->buildMappingCompilerPass(
            'EL\Game\Checkers\Entity',
             __DIR__.'/Resources/config/doctrine/',
            'Checkers'
        ));
    }
}
