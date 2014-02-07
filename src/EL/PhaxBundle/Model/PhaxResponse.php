<?php

namespace EL\PhaxBundle\Model;

use Symfony\Component\HttpFoundation\JsonResponse;
use EL\PhaxBundle\Model\PhaxReaction;

/**
 * Transform a PhaxReaction in symfony2 Response.
 */
class PhaxResponse extends JsonResponse
{
    public function __construct(PhaxReaction $phax_reaction)
    {
        parent::__construct($phax_reaction->jsonSerialize());
    }
}
