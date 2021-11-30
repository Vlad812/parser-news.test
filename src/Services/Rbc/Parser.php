<?php

namespace App\Services\Rbc;

use App\Services\AbstractParserBase;
use App\Services\ParsedDataContainer;

use DiDom\Exceptions\InvalidSelectorException;

use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class Parser
 * @package App\Services\Rbc
 */
class Parser extends AbstractParserBase implements ParserRbcInterface
{

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $imgDir
     * @return $this
     */
    public function setImgDir(string $imgDir): self
    {
        $this->imgDir = $imgDir;
        return $this;
    }

    /**
     * @param int $resourceId
     * @return $this
     */
    public function setResourceId(int $resourceId): self
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    /**
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    private function getIndexPageContent(): string
    {
        $this->parserLogger->info('Get content from : ' . $this->url);
        $response = $this->client->request('GET', $this->url);

        if(200 !== $response->getStatusCode()) {
            throw new Exception('Index page returned status code : ' . $response->getStatusCode() );
        }

        $this->parserLogger->info(print_r(['status code' => $response->getStatusCode()],true));

        return $response->getContent();
    }

    /**
     * @param string $contentHtml
     * @return Parser
     * @throws InvalidSelectorException
     * @throws Exception
     */
    private function parsingBlockNews(string $contentHtml): self
    {
        $this->parserLogger->info('Parsing block`s news is begin ... ');
        $this->document->loadHtml($contentHtml);

        $blockNewsNode = $this->document->find('div.js-news-feed-list')[0] ?? null;

        if(!$blockNewsNode){
            throw new Exception('Can`t find blockNewsNode : div.js-news-feed-list');
        }

        $itemsNewsNodeCollection = $blockNewsNode->find('a.news-feed__item');

        for ($indexItemNews = 0; $indexItemNews < 15; $indexItemNews++)
        {
            if (isset($itemsNewsNodeCollection[$indexItemNews]))
            {
                $itemNewsNode      = $itemsNewsNodeCollection[$indexItemNews];
                $containerInstance = ParsedDataContainer::getContainerInstance();

                $title = $itemNewsNode->find('span.news-feed__item__title')[0]->text();
                $link  = $itemNewsNode->attributes()['href'];

                $containerInstance->setTitle(trim($title))
                                  ->setLink(trim($link))
                                  ->setResourceId($this->resourceId)
                                  ->save();
            }
            else {
                $this->parserLogger->warning('indexItemNews : ' . $indexItemNews . 'is not exist');
            }
        }

        $this->parserLogger->info('Parsing block`s news is complete ... ');
        $this->parserLogger->info(print_r(ParsedDataContainer::getContainer(),true));

        return $this;
    }

    /**
     * @return $this
     * @throws ClientExceptionInterface
     * @throws InvalidSelectorException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    private function parsingArticles(): self
    {
        $this->parserLogger->info('Parsing article`s body is begin ... ');

        $parsedDataContainer = ParsedDataContainer::getContainer();

        $countNews          = count($parsedDataContainer);
        $currentNewsProcess = 0;

        foreach ($parsedDataContainer as $k => $v ) {

            $currentNewsProcess++;

            $this->parserLogger->info('Processing news: ' . $currentNewsProcess . ' from ' . $countNews);
            $this->parserLogger->info(print_r(['in_process' => $v->getLink()],true));

            $responseArticle = $this->client->request('GET', $v->getLink());

            if(200 !== $responseArticle->getStatusCode()) {
                $this->parserLogger->error('Parsing article...');
                throw new Exception('Returned Status Code : ' . $responseArticle->getStatusCode() );
            }

            $this->parserLogger->info(print_r(['status_code' => $responseArticle->getStatusCode()],true));

            $articleContent               = $this->document->loadHtml($responseArticle->getContent());
            $articleContentNodeCollection = $articleContent->find('div.article__content');

            if(!$articleContentNodeCollection) {
                $this->parserLogger->warning('Parsing article... Can`t find node : div.article__content');
            }

            $article = '';

            foreach ($articleContentNodeCollection as $indexNode => $articleNode) {

                    $paragraphCollection = $articleNode->find('p') ?? [];

                    if ($paragraphCollection) {
                        foreach ($paragraphCollection as $paragraph) {
                            $article .= trim($paragraph->text());
                        }

                        // у стандартных статей картинка находится в первом блоке div.article__content
                        if($indexNode === 0) {
                            $articleImageNode = $articleNode->find('div.article__main-image__wrap img')[0] ?? null;

                            if($articleImageNode) {
                                $articleImgSrc = $articleImageNode->attributes()['src'];
                                $nameImage     = $this->saveImageFile($articleImgSrc, $this->imgDir);
                                if($nameImage) {
                                    $v->setImgName($nameImage);
                                }
                            }
                            else {
                                $this->parserLogger->warning('articleImageNode is not found');
                            }
                        }
                    }
                    else {
                        $this->parserLogger->warning('articleParagraphNode is Not Found !');
                    }
                $v->setArticle($article);
            }
        }

        $this->parserLogger->info(print_r(ParsedDataContainer::getContainer(),true));

        return $this;
    }

    /**
     * @return Parser
     */
    public function saveToDataBase(): self
    {
        ParsedDataContainer::saveToDataBase($this->em);
        return $this;
    }

    /**
     * @return Parser
     * @throws InvalidSelectorException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function run(): self
    {
        $this->parserLogger->info('Running RBC Parser ... ');

        $contentHtml = $this->getIndexPageContent();

        $this->parsingBlockNews($contentHtml)
             ->parsingArticles();

        return $this;
    }
}
