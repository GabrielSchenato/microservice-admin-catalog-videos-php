<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Notification\NotificationException;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class VideoEntityUnitTest extends TestCase
{
    public function testAttributesCreate()
    {
        $title = 'New Video';
        $description = 'New Description';
        $yearLaunched = 2029;
        $duration = 12;
        $rating = Rating::RATE12;

        $video = new VideoEntity(
            title: $title,
            description: $description,
            yearLaunched: $yearLaunched,
            duration: $duration,
            opened: true,
            rating: $rating,
            published: true
        );

        $this->assertNotEmpty($video->id());
        $this->assertEquals($title, $video->title);
        $this->assertEquals($rating, $video->rating);
        $this->assertNotEmpty($video->createdAt());
    }

    public function testAttributesUpdate()
    {
        $uuid = RamseyUuid::uuid4();
        $title = 'New Video';
        $description = 'New Description';
        $yearLaunched = 2029;
        $duration = 12;
        $rating = Rating::RATE12;
        $date = date('Y-m-d H:i:s');

        $video = new VideoEntity(
            title: $title,
            description: $description,
            yearLaunched: $yearLaunched,
            duration: $duration,
            opened: false,
            rating: $rating,
            id: new Uuid($uuid),
            published: true,
            createdAt: new DateTime($date)
        );

        $this->assertEquals($uuid, $video->id());
        $this->assertEquals($title, $video->title);
        $this->assertFalse($video->opened);
        $this->assertEquals($date, $video->createdAt());
    }

    //    public function testUpdate()
    //    {
    //        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
    //        $createdAt = '2024-01-01 12:00:00';
    //
    //        $video = new VideoEntity(
    //            title: 'Old Video',
    //            type: VideoType::ACTOR,
    //            id: $uuid,
    //            createdAt: new DateTime($createdAt)
    //        );
    //        $video->update(
    //            title: 'New Video'
    //        );
    //
    //        $this->assertEquals($uuid, $video->id());
    //        $this->assertEquals($createdAt, $video->createdAt());
    //        $this->assertEquals('New Video', $video->title);
    //    }

    public function testException()
    {
        $this->expectException(NotificationException::class);
        $video = new VideoEntity(
            title: 'Ne',
            description: 'De',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true
        );
    }

    public function testAddCategory()
    {
        $categoryId = RamseyUuid::uuid4();

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true
        );

        $this->assertIsArray($video->categoriesId);
        $this->assertCount(0, $video->categoriesId);

        $video->addCategory(
            categoryId: $categoryId
        );
        $video->addCategory(
            categoryId: $categoryId
        );
        $this->assertCount(2, $video->categoriesId);
    }

    public function testRemoveCategory()
    {
        $categoryId1 = RamseyUuid::uuid4();
        $categoryId2 = RamseyUuid::uuid4();

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            categoriesId: [
                $categoryId1,
                $categoryId2,
            ]
        );

        $this->assertCount(2, $video->categoriesId);

        $video->removeCategory($categoryId1);

        $this->assertCount(1, $video->categoriesId);
        $this->assertEquals($video->categoriesId[1], $categoryId2);
    }

    public function testAddGenre()
    {
        $genreId = RamseyUuid::uuid4();

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true
        );

        $this->assertIsArray($video->genresId);
        $this->assertCount(0, $video->genresId);

        $video->addGenre(
            genreId: $genreId
        );
        $video->addGenre(
            genreId: $genreId
        );
        $this->assertCount(2, $video->genresId);
    }

    public function testRemoveGenre()
    {
        $genreId1 = RamseyUuid::uuid4();
        $genreId2 = RamseyUuid::uuid4();

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            genresId: [
                $genreId1,
                $genreId2,
            ]
        );

        $this->assertCount(2, $video->genresId);

        $video->removeGenre($genreId1);

        $this->assertCount(1, $video->genresId);
        $this->assertEquals($video->genresId[1], $genreId2);
    }

    public function testAddCastMember()
    {
        $castMemberId = RamseyUuid::uuid4();

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true
        );

        $this->assertIsArray($video->castMembersId);
        $this->assertCount(0, $video->castMembersId);

        $video->addCastMember(
            castMemberId: $castMemberId
        );
        $video->addCastMember(
            castMemberId: $castMemberId
        );
        $this->assertCount(2, $video->castMembersId);
    }

    public function testRemoveCastMember()
    {
        $castMemberId1 = RamseyUuid::uuid4();
        $castMemberId2 = RamseyUuid::uuid4();

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            castMembersId: [
                $castMemberId1,
                $castMemberId2,
            ]
        );

        $this->assertCount(2, $video->castMembersId);

        $video->removeCastMember($castMemberId1);

        $this->assertCount(1, $video->castMembersId);
        $this->assertEquals($video->castMembersId[1], $castMemberId2);
    }

    public function testValueObjectImageThumbFile()
    {
        $path = 'path/image-filmex.png';
        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            thumbFile: new Image(
                path: $path
            )
        );

        $this->assertNotNull($video->getThumbFile());
        $this->assertInstanceOf(Image::class, $video->getThumbFile());
        $this->assertEquals($path, $video->getThumbFile()->getPath());
    }

    public function testValueObjectImageToThumbHalf()
    {
        $path = 'path/image-filmex.png';
        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            thumbHalf: new Image(
                path: $path
            )
        );

        $this->assertNotNull($video->getThumbHalf());
        $this->assertInstanceOf(Image::class, $video->getThumbHalf());
        $this->assertEquals($path, $video->getThumbHalf()->getPath());
    }

    public function testValueObjectImageBannerFile()
    {
        $path = 'path/image-filmex.png';
        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            bannerFile: new Image(
                path: $path
            )
        );

        $this->assertNotNull($video->getBannerFile());
        $this->assertInstanceOf(Image::class, $video->getBannerFile());
        $this->assertEquals($path, $video->getBannerFile()->getPath());
    }

    public function testValueObjectMediaTrailerFile()
    {
        $trailerFile = new Media(
            filePath: 'path/video.mp4',
            mediaStatus: MediaStatus::PENDING,
            encodedPath: 'path/encoded.extension'
        );

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            trailerFile: $trailerFile
        );

        $this->assertNotNull($video->getTrailerFile());
        $this->assertInstanceOf(Media::class, $video->getTrailerFile());
        $this->assertEquals($trailerFile->filePath, $video->getTrailerFile()->filePath);
    }

    public function testValueObjectMediaVideoFile()
    {
        $videoFile = new Media(
            filePath: 'path/video.mp4',
            mediaStatus: MediaStatus::PENDING
        );

        $video = new VideoEntity(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: false,
            rating: Rating::RATE10,
            published: true,
            videoFile: $videoFile
        );

        $this->assertNotNull($video->getVideoFile());
        $this->assertInstanceOf(Media::class, $video->getVideoFile());
        $this->assertEquals($videoFile->filePath, $video->getVideoFile()->filePath);
    }
}
