<?php

namespace EL\Bundle\Game\TicTacToeBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use EL\Bundle\CoreBundle\AbstractGame\Bundle;

class GameTicTacToeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass($this->buildMappingCompilerPass(
            'EL\Game\TicTacToe\Entity',
             __DIR__.'/Resources/config/doctrine/',
            'TicTacToe'
        ));
    }
}
