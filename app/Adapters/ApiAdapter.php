<?php

namespace App\Adapters;

use App\Http\Resources\DefaultResource;
use Core\Domain\Repository\PaginationInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;


class ApiAdapter
{
    public function __construct(
        // private ?PaginationInterface $response = null
        private readonly PaginationInterface $response
    )
    {
    }

    public function toJson(): AnonymousResourceCollection
    {
        // if (!$this->response) {
        //     throw new \Exception('Response is null');
        // }

        return DefaultResource::collection($this->response->items())
            ->additional([
                'meta' => [
                    'total' => $this->response->total(),
                    'current_page' => $this->response->currentPage(),
                    'last_page' => $this->response->lastPage(),
                    'first_page' => $this->response->firstPage(),
                    'per_page' => $this->response->perPage(),
                    'to' => $this->response->to(),
                    'from' => $this->response->from(),
                ],
            ]);
    }

    public function toXml()
    {
        //
    }

    public static function json(object $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return (new DefaultResource($data))
            ->response()
            ->setStatusCode($statusCode);
    }
}
