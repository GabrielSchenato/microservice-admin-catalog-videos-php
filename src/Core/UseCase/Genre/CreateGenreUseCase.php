<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\GenreEntity;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\CreateGenre\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\CreateGenre\GenreCreateOutputDto;
use Core\UseCase\Interfaces\TransactionDbInterface;

class CreateGenreUseCase
{
    public function __construct(
        protected GenreRepositoryInterface    $repository,
        protected TransactionDbInterface      $transaction,
        protected CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    /**
     * @throws EntityValidationException
     */
    public function execute(GenreCreateInputDto $input): GenreCreateOutputDto
    {
        try {
            $genre = new GenreEntity(
                name: $input->name,
                isActive: $input->isActive,
                categoriesId: $input->categoriesId
            );

            $this->validateCategoriesId($input->categoriesId);

            $newGenre = $this->repository->insert($genre);

            $genreCreateOutputDto = new GenreCreateOutputDto(
                id: $newGenre->id(),
                name: $newGenre->name,
                is_active: $newGenre->isActive,
                created_at: $newGenre->createdAt()
            );

            $this->transaction->commit();

            return $genreCreateOutputDto;
        } catch (\Throwable $th) {
            $this->transaction->rollback();
            throw $th;
        }
    }

    /**
     * @throws NotFoundException
     */
    public function validateCategoriesId(array $categoriesId = []): void
    {
        $categoriesDb = $this->categoryRepository->getIdsListIds($categoriesId);

        $arrayDiff = array_diff($categoriesId, $categoriesDb);
        $count = count($arrayDiff);
        if ($count > 0) {
            $msg = sprintf(
                '%s %s not found',
                $count > 1 ? 'Categories' : 'Category',
                implode(',', $arrayDiff)
            );
            throw new NotFoundException($msg);
        }
    }
}
