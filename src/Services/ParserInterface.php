<?php

namespace App\Services;

/**
 * Interface ParserInterface
 * @package App\Services
 */
interface ParserInterface
{
    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self;

    /**
     * @param string $imgDir
     * @return $this
     */
    public function setImgDir(string $imgDir): self;

    /**
     * @param $resourceId
     * @return mixed
     */
    public function setResourceId(int $resourceId): self;

    /**
     * @return $this
     */
    public function run(): self;

    /**
     * @return $this
     */
    public function saveToDataBase(): self;
}
