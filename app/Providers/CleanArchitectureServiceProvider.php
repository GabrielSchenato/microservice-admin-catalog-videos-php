<?php

namespace App\Providers;

use App\Events\VideoEvent;
use App\Services\AMQP\AMQPInterface;
use App\Services\AMQP\PhpAmqpService;
use App\Repositories\Eloquent\{CastMemberEloquentRepository,
    CategoryEloquentRepository,
    GenreEloquentRepository,
    VideoEloquentRepository};
use App\Repositories\Transaction\TransactionDb;
use App\Services\Storage\FileStorage;
use Core\Domain\Repository\{CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface};
use Core\UseCase\Interfaces\{FileStorageInterface, TransactionDbInterface};
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Illuminate\Support\ServiceProvider;

class CleanArchitectureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->bindRepositories();

        $this->app->singleton(
            FileStorageInterface::class,
            FileStorage::class
        );

        $this->app->singleton(
            VideoEventManagerInterface::class,
            VideoEvent::class
        );

        $this->app->bind(
            TransactionDbInterface::class,
            TransactionDb::class
        );


        $this->app->bind(
            AMQPInterface::class,
            PhpAmqpService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * @return void
     */
    protected function bindRepositories(): void
    {
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryEloquentRepository::class
        );
        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreEloquentRepository::class
        );
        $this->app->singleton(
            CastMemberRepositoryInterface::class,
            CastMemberEloquentRepository::class
        );
        $this->app->singleton(
            VideoRepositoryInterface::class,
            VideoEloquentRepository::class
        );
    }
}
