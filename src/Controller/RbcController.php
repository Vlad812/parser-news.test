<?php

namespace App\Controller;

use App\Services\Rbc\ParserRbcInterface;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RbcController
 * @package App\Controller
 */
class RbcController extends AbstractBaseController
{
    /**
     *
     */
    private const RESOURCE_NAME = 'RBC';

    /**
     *
     */
    private const URL_RBC = 'https://www.rbc.ru/';

    /**
     *  Дириктория для сохранения изображений
     *  Будет сохранено в 'public/img/rbc/'
     */
    private const IMG_DIR_RBC = 'img/rbc/';

    /**
     * Запуск парсера
     *
     * @Route("/rbc_parser")
     * @param EntityManagerInterface $em
     * @param LoggerInterface $parserLogger
     * @param ParserRbcInterface $parserRbcService
     * @return Response
     */
    public function parserRunAction(EntityManagerInterface $em,
                                    LoggerInterface        $parserLogger,
                                    ParserRbcInterface     $parserRbcService): Response
    {
        try {
            $resourceId = $em->getRepository('App:NewsResourceReference')
                             ->findOneByName(self::RESOURCE_NAME)
                             ->getId();

            $parserRbcService->setUrl(self::URL_RBC)
                             ->setImgDir(self::IMG_DIR_RBC)
                             ->setResourceId($resourceId)
                             ->run()
                             ->saveToDataBase();
        }
        catch (Exception $e) {
            $parserLogger->debug(print_r($e,true));
            return $this->response->setStatusCode(500)
                                  ->setContent('Something wrong in during parsing .... ');
        }
        return $this->response->setContent('Ok');
    }

    /**
     *  Список Новостей
     *
     * @Route("/rbc_news")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function showNewsListAction(EntityManagerInterface $em): Response
    {
        $resourceId = $em->getRepository('App:NewsResourceReference')
                         ->findOneByName(self::RESOURCE_NAME)
                         ->getId();

        $newsList = $em->getRepository('App:News')
                       ->getNewsListByResourceId($resourceId);

        return $this->render('rbc/news_list.html.twig',
                             ['newsList' => $newsList ?? []]
        );
    }

    /**
     *  Полная новость
     *
     * @Route("/rbc_article/{id}")
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    public function showArticleAction(EntityManagerInterface $em, int $id): Response
    {
        $article = $em->getRepository('App:News')->find($id);

        return $this->render('rbc/article.html.twig',
                                  ['article' => $article ?? null , 'imgDir' => self::IMG_DIR_RBC]
        );
    }
}
