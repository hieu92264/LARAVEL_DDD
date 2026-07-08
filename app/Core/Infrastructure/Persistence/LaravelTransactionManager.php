<?php

namespace App\Core\Infrastructure\Persistence;

use App\Core\Application\Contracts\TransactionManager;
use Illuminate\Support\Facades\DB;
use Throwable;
use Closure;

final class LaravelTransactionManager implements TransactionManager
{
    /**
     * @throws Throwable
     * => laravel tu dong rollback khi callback nem ra exception hoac tu commit neu call back thanh
     * cong. Tham so attempts: cho phep thu lai khi gap deadlock
     */
    public function run(Closure $callback): mixed
    {
        return DB::transaction(
            $callback, attempts: 3
        );
    }
}
