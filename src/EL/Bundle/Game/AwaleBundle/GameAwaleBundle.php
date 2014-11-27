<?php

namespace EL\Bundle\Game\AwaleBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use EL\Bundle\CoreBundle\AbstractGame\Bundle;

class GameAwaleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass($this->buildMappingCompilerPass(
            'EL\Game\Awale\Entity',
             __DIR__.'/Resources/config/doctrine/',
            'Awale'
        ));
        
        $container->addCompilerPass($this->buildYamlFixturesCompilerPass('GameAwaleBundle'));
    }
}
