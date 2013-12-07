<?php

namespace EL\PhaxBundle\Model;

use Symfony\Component\HttpFoundation\JsonResponse;
use EL\PhaxBundle\Model\PhaxReaction;


class PhaxResponse extends JsonResponse
{
    public function __construct(PhaxReaction $phax_reaction) {
        parent::__construct($phax_reaction->getJsonData());
    }
}