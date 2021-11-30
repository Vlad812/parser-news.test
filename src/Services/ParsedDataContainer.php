<?php

namespace App\Services;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ParsedDataContainer
 * @package App\Services
 */
class ParsedDataContainer
{
    /**
     * @var array
     */
    private static array $container = [];

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $link;

    /**
     * @var string|null
     */
    private ?string $article = null;

    /**
     * @var string|null
     */
    private ?string $imgName = null;

    /**
     * @var int
     */
    private int $resourceId;

    /**
     * @return self
     */
    public static function getContainerInstance(): self
    {
        return new self();
    }

    /**
     * @return array
     */
    public static function getContainer(): array
    {
        return self::$container;
    }

    /**
     * @return void
     */
    public function save(): void
    {
        array_push(self::$container, $this);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $link
     * @return $this
     */
    public function setLink(string $link): self
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $imgName
     * @return $this
     */
    public function setImgName(string $imgName): self
    {
        $this->imgName = $imgName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImgName(): ?string
    {
        return $this->imgName;
    }

    /**
     * @param string $article
     * @return $this
     */
    public function setArticle(string $article): self
    {
        $this->article = $article;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getArticle(): ?string
    {
        return $this->article;
    }

    /**
     * @return int
     */
    public function getResourceId(): int
    {
        return $this->resourceId;
    }

    /**
     * @param int $resourceId
     * @return ParsedDataContainer
     */
    public function setResourceId(int $resourceId): self
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public static function saveToDataBase(EntityManagerInterface $em): void
    {
        foreach (self::$container as $i => $itemNews) {
            $newsItemInstance  = new News();
            $newsItemInstance->setTitle($itemNews->getTitle())
                             ->setLink($itemNews->getLink())
                             ->setArticle($itemNews->getArticle())
                             ->setImg($itemNews->getImgName())
                             ->setResourceId($itemNews->getResourceId());

            $em->persist($newsItemInstance);
        }

        $em->flush();
        $em->clear();
    }
}
