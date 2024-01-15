<?php

namespace App\Repositories\Eloquent;

use App\Models\Video;
use App\Repositories\Eloquent\Traits\VideoTrait;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Builder\Video\UpdateVideoBuilder;
use Core\Domain\Entity\AbstractEntity;
use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;

class VideoEloquentRepository implements VideoRepositoryInterface
{
    use VideoTrait;

    public function __construct(protected Video $model)
    {
    }

    public function insert(AbstractEntity $entity): VideoEntity
    {
        $entityDb = $this->model->create([
            'id' => $entity->id(),
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'rating' => $entity->rating->value,
            'duration' => $entity->duration,
            'opened' => $entity->opened,
        ]);

        $this->syncRelationships($entityDb, $entity);

        return $this->toVideo($entityDb);
    }

    public function findById(string $id): VideoEntity
    {
        if (!$video = $this->model->find($id)) {
            throw new NotFoundException();
        }

        return $this->toVideo($video);
    }

    public function update(AbstractEntity $entity): VideoEntity
    {
        if (!$videoDb = $this->model->find($entity->id())) {
            throw new NotFoundException('Video Not Found');
        }

        $videoDb->update([
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'rating' => $entity->rating->value,
            'duration' => $entity->duration,
            'opened' => $entity->opened,
        ]);

        $videoDb->refresh();

        $this->syncRelationships($videoDb, $entity);

        return $this->toVideo($videoDb);
    }

    public function delete(string $id): bool
    {
        if (!$videoDb = $this->model->find($id)) {
            throw new NotFoundException('Video Not Found');
        }
        return $videoDb->delete();
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $videos = $this->model
            ->when($filter, fn($query) => $query->where('title', 'LIKE', "%{$filter}%"))
            ->orderBy('id', $order)
            ->get();

        return $videos->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $paginator = $this->model
            ->when($filter, fn($query) => $query->where('title', 'LIKE', "%{$filter}%"))
            ->with([
                'media',
                'trailer',
                'banner',
                'thumb',
                'thumbHalf',
                'categories',
                'castMembers',
                'genres',
            ])
            ->orderBy('title', $order)
            ->paginate($totalPage, ['*'], 'page', $page);

        return new PaginationPresenter($paginator);
    }

    public function updateMedia(AbstractEntity $entity): AbstractEntity
    {
        if (!$objectModel = $this->model->find($entity->id())) {
            throw new NotFoundException('Video not found');
        }

        $this->updateMediaVideo($entity, $objectModel);
        $this->updateMediaTrailer($entity, $objectModel);

        $this->updateImageBanner($entity, $objectModel);
        $this->updateImageThumb($entity, $objectModel);
        $this->updateImageThumbHalf($entity, $objectModel);

        return $this->toVideo($objectModel);
    }

    protected function syncRelationships(Video $model, AbstractEntity $entity): void
    {
        $model->categories()->sync($entity->categoriesId);
        $model->genres()->sync($entity->genresId);
        $model->castMembers()->sync($entity->castMembersId);
    }

    private function toVideo(object $object): VideoEntity
    {
        $entity = new VideoEntity(
            title: $object->title,
            description: $object->description,
            yearLaunched: (int)$object->year_launched,
            duration: (bool)$object->duration,
            opened: $object->opened,
            rating: Rating::from($object->rating),
            id: new Uuid($object->id)
        );
        foreach ($object->categories as $category) {
            $entity->addCategory($category->id);
        }

        foreach ($object->genres as $genre) {
            $entity->addGenre($genre->id);
        }

        foreach ($object->castMembers as $castMember) {
            $entity->addCastMember($castMember->id);
        }

        $builder = (new UpdateVideoBuilder())
            ->setEntity($entity);

        if ($trailer = $object->trailer) {
            $builder->addTrailer($trailer->file_path);
        }

        if ($mediaVideo = $object->media) {
            $builder->addMediaVideo(
                path: $mediaVideo->file_path,
                mediaStatus: MediaStatus::from($mediaVideo->media_status),
                encodedPath: $mediaVideo->encoded_path
            );
        }

        if ($banner = $object->banner) {
            $builder->addBanner($banner->path);
        }

        if ($thumb = $object->thumb) {
            $builder->addThumb($thumb->path);
        }

        if ($thumbHalf = $object->thumbHalf) {
            $builder->addThumbHalf($thumbHalf->path);
        }

        return $builder->getEntity();
    }
}
