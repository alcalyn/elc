<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class PhaxController extends Controller
{
    public function phaxAction($_locale)
    {
        return new JsonResponse(array(
            'ok'        => 'ouais',
            'locale'    => $_locale,
        ));
    }
}
