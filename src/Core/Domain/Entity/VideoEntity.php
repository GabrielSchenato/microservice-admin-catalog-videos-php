<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Enum\Rating;
use Core\Domain\Factory\VideoValidatorFactory;
use Core\Domain\Notification\NotificationException;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class VideoEntity extends Entity
{
    use MethodsMagicsTrait;

    /**
     * @throws NotificationException
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
        protected ?Image    $bannerFile = null,
        protected ?Media    $trailerFile = null,
        protected ?Media    $videoFile = null,
    )
    {
        parent::__construct();

        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    /**
     * @throws NotificationException
     */
    private function validate(): void
    {
        VideoValidatorFactory::create()->validate($this);
        if ($this->notification->hasErrors()) {
            throw new NotificationException($this->notification->messages('video'));
        }
    }

    /**
     * @throws NotificationException
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
     * @return Image|null
     */
    public function getBannerFile(): ?Image
    {
        return $this->bannerFile;
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
