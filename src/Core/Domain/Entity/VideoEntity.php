<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Exception;

class VideoEntity
{
    use MethodsMagicsTrait;

    /**
     * @throws EntityValidationException
     * @throws Exception
     */
    public function __construct(
        protected string    $title,
        protected string    $description,
        protected int       $yearLaunched,
        protected int       $duration,
        protected bool      $opened,
        protected Rating    $rating,
        protected ?Uuid     $id = null,
        protected bool      $published = false,
        protected ?DateTime $createdAt = null,
        protected array     $categoriesId = [],
        protected array     $genresId = [],
        protected array     $castMembersId = [],
        protected ?Image    $thumbFile = null,
        protected ?Image    $thumbHalf = null,
        protected ?Media    $trailerFile = null,
        protected ?Media    $videoFile = null,
    )
    {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    /**
     * @throws EntityValidationException
     */
    private function validate(): void
    {
        DomainValidation::strMaxLength($this->title);
        DomainValidation::strMinLength($this->title);
        DomainValidation::strCanNullAndMaxLength($this->description);
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function disabled(): void
    {
        $this->isActive = false;
    }

    /**
     * @throws EntityValidationException
     */
    public function update(string $title, string $description = ''): void
    {
        $this->title = $title;
        $this->description = $description;

        $this->validate();
    }

    public function addCategory(string $categoryId): void
    {
        $this->categoriesId[] = $categoryId;
    }

    public function removeCategory(string $categoryId): void
    {
        unset($this->categoriesId[array_search($categoryId, $this->categoriesId)]);
    }

    public function addGenre(string $genreId): void
    {
        $this->genresId[] = $genreId;
    }

    public function removeGenre(string $genreId): void
    {
        unset($this->genresId[array_search($genreId, $this->genresId)]);
    }

    public function addCastMember(string $castMemberId): void
    {
        $this->castMembersId[] = $castMemberId;
    }

    public function removeCastMember(string $castMemberId): void
    {
        unset($this->castMembersId[array_search($castMemberId, $this->castMembersId)]);
    }

    /**
     * @return Image|null
     */
    public function getThumbFile(): ?Image
    {
        return $this->thumbFile;
    }

    /**
     * @return Image|null
     */
    public function getThumbHalf(): ?Image
    {
        return $this->thumbHalf;
    }

    /**
     * @return Media|null
     */
    public function getTrailerFile(): ?Media
    {
        return $this->trailerFile;
    }

    /**
     * @return Media|null
     */
    public function getVideoFile(): ?Media
    {
        return $this->videoFile;
    }
}
