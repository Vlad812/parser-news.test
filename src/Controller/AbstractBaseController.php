<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractBaseController
 * @package App\Controller
 */
class AbstractBaseController extends AbstractController
{
    /**
     * @var Response
     */
    protected Response $response;

    /**
     * AbstractBaseController constructor.
     */
    public function __construct()
    {
        $this->response = new Response();
    }
}
