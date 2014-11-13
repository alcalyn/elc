<?php

namespace EL\Bundle\CoreBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use EL\Bundle\CoreBundle\Services\GameService;
use EL\Bundle\CoreBundle\Exception\ELCoreException;

class GameServiceConverter implements ParamConverterInterface
{
    /**
     * @var Container
     */
    private $container;
    
    /**
     * @var GameService
     */
    private $gameService;
    
    public function __construct(Container $container)
    {
        $this->container    = $container;
        $this->gameService  = $container->get('el_core.game');
    }

    /**
     * @{inheritdoc}
     *
     * @throws NotFoundHttpException When game with this slug not found
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $options    = $this->getOptions($configuration);
        $locale     = $request->getLocale();
        $slug       = $request->attributes->get($options['parameter']);
        
        if (empty($slug)) {
            return false;
        }
        
        try {
            $this->gameService->setGameBySlug($slug, $locale);
        } catch (ELCoreException $e) {
            throw new NotFoundHttpException('Game "'.$slug.'" not installed');
        }

        $request->attributes->set($configuration->getName(), $this->gameService);

        return true;
    }

    /**
     * @{inheritdoc}
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return 'EL\Bundle\CoreBundle\Services\GameService' === $configuration->getClass();
    }
    
    /**
     * Use Doctrine getOptions to return default values for non-provided values
     * 
     * @param ConfigurationInterface $configuration
     * @return array
     */
    protected function getOptions(ConfigurationInterface $configuration)
    {
        return array_replace(array(
            'parameter' => 'slug',
        ), $configuration->getOptions());
    }
}
