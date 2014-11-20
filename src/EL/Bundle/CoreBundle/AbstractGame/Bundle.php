<?php

namespace EL\Bundle\CoreBundle\AbstractGame;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

abstract class Bundle extends BaseBundle
{
    /**
     * Build doctrine yaml mapping pass for a game
     * 
     * @param string $entityNamespace (i.e 'EL\Game\MyGame\Entity')
     * @param string $mappingDir where are yaml mappings (i.e __DIR__.'/Resources/config/doctrine/')
     * @param string $entityNamespaceAlias used for doctrine repository name (i.e 'MyGame')
     * 
     * @return DoctrineOrmMappingsPass
     */
    protected function buildMappingCompilerPass($entityNamespace, $mappingDir, $entityNamespaceAlias)
    {
        return DoctrineOrmMappingsPass::createYamlMappingDriver(array(
            $mappingDir => $entityNamespace,
        ), array(), false, array(
            $entityNamespaceAlias => $entityNamespace,
        ));
    }
}
