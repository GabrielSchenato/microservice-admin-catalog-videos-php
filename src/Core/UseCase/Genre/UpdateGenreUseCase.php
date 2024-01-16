<?php

namespace Core\UseCase\Genre;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\UpdateGenre\GenreUpdateInputDto;
use Core\UseCase\DTO\Genre\UpdateGenre\GenreUpdateOutputDto;
use Core\UseCase\Interfaces\TransactionDbInterface;

class UpdateGenreUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository,
        protected TransactionDbInterface $transaction,
        protected CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @throws EntityValidationException
     */
    public function execute(GenreUpdateInputDto $input): GenreUpdateOutputDto
    {
        $genre = $this->repository->findById($input->id);
        try {
            $genre->update(name: $input->name);
            foreach ($input->categoriesId as $categoryId) {
                $genre->addCategory($categoryId);
            }

            $this->validateCategoriesId($input->categoriesId);

            $newGenre = $this->repository->update($genre);

            $genreUpdateOutputDto = new GenreUpdateOutputDto(
                id: $newGenre->id(),
                name: $newGenre->name,
                is_active: $newGenre->isActive,
                created_at: $newGenre->createdAt()
            );

            $this->transaction->commit();

            return $genreUpdateOutputDto;
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
