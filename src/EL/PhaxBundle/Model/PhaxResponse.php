<?php

namespace EL\PhaxBundle\Model;

use Symfony\Component\HttpFoundation\JsonResponse;


class PhaxResponse extends JsonResponse
{
    public function __construct($data = null)
    {
        parent::__construct(self::createMetadata() + $data);
    }
    
    private static function createMetadata()
    {
        return array(
            'phax_metadata' => array(
                'has_error'             => false,
                'errors'                => array(),
                'trigger_js_reaction'   => true,
            ),
        );
    }
    
}