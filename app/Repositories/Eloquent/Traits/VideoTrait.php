<?php

namespace App\Repositories\Eloquent\Traits;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Core\Domain\Entity\VideoEntity;
use Illuminate\Database\Eloquent\Model;

trait VideoTrait
{
    public function updateMediaVideo(VideoEntity $entity, Model $model): void
    {
        if ($mediaVideo = $entity->getVideoFile()) {
            $action = $model->media()->first() ? 'update' : 'create';
            $model->media()->{$action}([
                'file_path' => $mediaVideo->filePath,
                'media_status' => (string) $mediaVideo->mediaStatus->value,
                'encoded_path' => $mediaVideo->encodedPath,
                'type' => (string) MediaTypes::VIDEO->value,
            ]);
        }
    }

    public function updateMediaTrailer(VideoEntity $entity, Model $model): void
    {
        if ($trailer = $entity->getTrailerFile()) {
            $action = $model->trailer()->first() ? 'update' : 'create';
            $model->trailer()->{$action}([
                'file_path' => $trailer->filePath,
                'media_status' => (string) $trailer->mediaStatus->value,
                'encoded_path' => $trailer->encodedPath,
                'type' => (string) MediaTypes::TRAILER->value,
            ]);
        }
    }

    public function updateImageBanner(VideoEntity $entity, Model $model): void
    {
        if ($banner = $entity->getBannerFile()) {
            $action = $model->banner()->first() ? 'update' : 'create';
            $model->banner()->{$action}([
                'path' => $banner->getPath(),
                'type' => (string) ImageTypes::BANNER->value,
            ]);
        }
    }

    public function updateImageThumb(VideoEntity $entity, Model $model): void
    {
        if ($thumb = $entity->getThumbFile()) {
            $action = $model->thumb()->first() ? 'update' : 'create';
            $model->thumb()->{$action}([
                'path' => $thumb->getPath(),
                'type' => (string) ImageTypes::THUMB->value,
            ]);
        }
    }

    public function updateImageThumbHalf(VideoEntity $entity, Model $model): void
    {
        if ($thumbHalf = $entity->getThumbHalf()) {
            $action = $model->thumbHalf()->first() ? 'update' : 'create';
            $model->thumbHalf()->{$action}([
                'path' => $thumbHalf->getPath(),
                'type' => (string) ImageTypes::THUMB_HALF->value,
            ]);
        }
    }
}
