<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SomeNewsController
 * @package App\Controller
 */
class SomeNewsController extends AbstractBaseController
{
    /**
     *
     */
    private const RESOURCE_NAME = 'SomeNews';

    /**
     *
     */
    private const URL_SOME_NEWS = 'https://www.some-news.ru/';

    /**
     * дириктория для сохранения изображений
     * 'public/img/rbc/'
     */
    private const IMG_DIR_SOME_NEWS = 'img/SomeNews/';

    /**
     * Запуск парсера
     *
     * @Route("/some_news_parser")
     * @return void
     */
    public function parserRunAction()
    {

    }
}
