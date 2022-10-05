<?php

declare(strict_types=1);

namespace Khazhinov\LaravelLightyMongoDBBundle\Transaction;

use Closure;
use Illuminate\Support\Facades\DB;
use MongoDB\Client;
use MongoDB\Driver\Session;
use Throwable;

trait WithMongoDBTransaction
{
    protected Session $current_session;

    public function beginTransaction(): void
    {
        /** @var Client $clinet */
        $clinet = DB::getMongoClient();
        $this->current_session = $clinet->startSession();

        $this->current_session?->startTransaction();
    }

    public function commit(): void
    {
        $this->current_session?->commitTransaction();
    }

    public function rollback(): void
    {
        $this->current_session?->abortTransaction();
    }

    /**
     * @throws Throwable
     */
    public function transaction(Closure $closure): mixed
    {
        $this->beginTransaction();

        try {
            $result = $closure();

            $this->commit();

            return $result;
        } catch (Throwable $exception) {
            $this->rollback();

            throw new $exception();
        }
    }
}
