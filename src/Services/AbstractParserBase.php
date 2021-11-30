<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use DiDom\Document;

/**
 * Class AbstractParserBase
 * @package App\Services
 */
abstract class AbstractParserBase
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $parserLogger;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var HttpClientInterface
     */
    protected HttpClientInterface $client;

    /**
     * @var string
     */
    protected string $url;

    /**
     * @var string
     */
    protected string $imgDir;

    /**
     * @var int
     */
    protected int $resourceId;

    /**
     * @var Document
     */
    protected Document $document;

    /**
     * AbstractParserBase constructor.
     * @param LoggerInterface $parserLogger
     * @param EntityManagerInterface $em
     * @param HttpClientInterface $client
     */
    public function __construct(LoggerInterface        $parserLogger,
                                EntityManagerInterface $em,
                                HttpClientInterface    $client
    )
    {
        $this->parserLogger = $parserLogger;
        $this->em           = $em;
        $this->client       = $client;
        $this->document     = new Document();
    }

    /**
     * @param string $src
     * @param string $dir
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function saveImageFile(string $src, string $dir): ?string
    {
        try {
            $response = $this->client->request('GET', $src);

            if(200 !== $response->getStatusCode()) {
                throw new \Exception('Returned Status Code : ' . $response->getStatusCode() );
            }

            $nameImage = md5($src) . '.jpg';

            $fileHandler = fopen($dir . $nameImage, 'w');
            fwrite($fileHandler, $response->getContent());
        }
        catch (\Exception $e) {

            $this->parserLogger->error('Save Image File Error !');
            $this->parserLogger->debug(print_r($e, true));

            return null;
        }
        return $nameImage;
    }
}
