<?php

namespace EL\Bundle\CoreBundle\AbstractGame\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterYamlFixturesPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $bundleName;
    
    /**
     * @param string $bundleName The name of the game bundle
     */
    public function __construct($bundleName)
    {
        $this->bundleName = $bundleName;
    }
    
    public function process(ContainerBuilder $container)
    {
        $yamlFixturesConfigs = $container->getExtensionConfig('khepin_yaml_fixtures');
        $yamlFixturesConfig = array_pop($yamlFixturesConfigs);
        $yamlFixturesConfig['resources'] []= $this->bundleName;
        $container->prependExtensionConfig('khepin_yaml_fixtures', $yamlFixturesConfig);
    }
}
