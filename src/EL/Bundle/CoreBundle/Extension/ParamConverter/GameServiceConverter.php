<?php

namespace EL\Bundle\CoreBundle\Extension\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use EL\Core\Service\GameService;
use EL\Core\Exception\Exception;

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
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options    = $this->getOptions($configuration);
        $locale     = $request->getLocale();
        $slug       = $request->attributes->get($options['parameter']);
        
        if (empty($slug)) {
            return false;
        }
        
        try {
            $this->gameService->setGameBySlug($slug, $locale);
        } catch (Exception $e) {
            throw new NotFoundHttpException('Game "'.$slug.'" not installed');
        }

        $request->attributes->set($configuration->getName(), $this->gameService);

        return true;
    }

    /**
     * @{inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return 'EL\Core\Service\GameService' === $configuration->getClass();
    }
    
    /**
     * Use Doctrine getOptions to return default values for non-provided values
     * 
     * @param ParamConverter $configuration
     * @return array
     */
    protected function getOptions(ParamConverter $configuration)
    {
        return array_replace(array(
            'parameter' => 'slug',
        ), $configuration->getOptions());
    }
}
