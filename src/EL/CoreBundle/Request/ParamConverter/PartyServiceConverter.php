<?php

namespace EL\CoreBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use EL\CoreBundle\Services\PartyService;
use EL\CoreBundle\Exception\ELCoreException;

class PartyServiceConverter implements ParamConverterInterface
{
    /**
     * @var Container
     */
    private $container;
    
    /**
     * @var PartyService
     */
    private $partyService;
    
    public function __construct(Container $container)
    {
        $this->container    = $container;
        $this->partyService = $container->get('el_core.party');
    }

    /**
     * @{inheritdoc}
     *
     * @throws NotFoundHttpException When game/party with these slugs not found
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $options    = $this->getOptions($configuration);
        $locale     = $request->getLocale();
        $slugGame   = $request->attributes->get($options['slugGame']);
        $slugParty  = $request->attributes->get($options['slugParty']);
        
        if (empty($slugGame) || empty($slugParty)) {
            return false;
        }
        
        try {
            $this->partyService->setPartyBySlug($slugParty, $slugGame, $locale, $this->container);
        } catch (ELCoreException $e) {
            throw new NotFoundHttpException('No party "'.$slugParty.'" for game "'.$slugGame.'"');
        }

        $request->attributes->set($configuration->getName(), $this->partyService);

        return true;
    }

    /**
     * @{inheritdoc}
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return 'EL\CoreBundle\Services\PartyService' === $configuration->getClass();
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
            'slugGame'  => 'slugGame',
            'slugParty' => 'slugParty',
        ), $configuration->getOptions());
    }
}
