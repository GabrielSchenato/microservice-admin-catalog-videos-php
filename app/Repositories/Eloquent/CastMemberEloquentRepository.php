<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{
    public function __construct(private CastMember $model)
    {
    }

    public function insert(CastMemberEntity $castMember): CastMemberEntity
    {
        $castMember = $this->model->create([
            'id' => $castMember->id,
            'name' => $castMember->name,
            'type' => $castMember->type->value,
            'created_at' => $castMember->createdAt(),
        ]);

        return $this->toCastMember($castMember);
    }

    public function findById(string $id): CastMemberEntity
    {
        if (!$castMember = $this->model->find($id)) {
            throw new NotFoundException();
        }

        return $this->toCastMember($castMember);
    }

    public function getIdsListIds(array $castMembersId = []): array
    {
        return $this->model
            ->whereIn('id', $castMembersId)
            ->pluck('id')
            ->toArray();
    }

    public function update(CastMemberEntity $castMember): CastMemberEntity
    {
        if (!$castMemberDb = $this->model->find($castMember->id())) {
            throw new NotFoundException('Cast Member Not Found');
        }

        $castMemberDb->update([
            'name' => $castMember->name,
        ]);

        $castMemberDb->refresh();

        return $this->toCastMember($castMemberDb);
    }

    public function delete(string $id): bool
    {
        if (!$castMemberDb = $this->model->find($id)) {
            throw new NotFoundException('Cast Member Not Found');
        }
        return $castMemberDb->delete();
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $categories = $this->model
            ->when($filter, fn($query) => $query->where('name', 'LIKE', "%{$filter}%"))
            ->orderBy('id', $order)
            ->get();

        return $categories->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $paginator = $this->model
            ->when($filter, fn($query) => $query->where('name', 'LIKE', "%{$filter}%"))
            ->orderBy('id', $order)
            ->paginate($totalPage);

        return new PaginationPresenter($paginator);
    }

    private function toCastMember(object $object): CastMemberEntity
    {
        return new CastMemberEntity(
            name: $object->name,
            type: CastMemberType::from($object->type),
            id: new Uuid($object->id),
            createdAt: $object->created_at
        );
    }
}
