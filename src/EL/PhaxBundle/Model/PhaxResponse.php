<?php

namespace EL\PhaxBundle\Model;

use Symfony\Component\HttpFoundation\JsonResponse;


class PhaxResponse extends JsonResponse {
    
    public function __construct($data = null, $status = 200, $headers = array())
    {
        parent::__construct($data, $status, $headers);
    }
    
}