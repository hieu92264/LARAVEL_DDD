<?php

declare(strict_types=1);

namespace App\Core\Application\Contracts;

interface TransactionManager
{
    /**
     * @template T
     *
     * @param Closure(): T $callback
     *
     * @return T
     */
    public function run(\Closure $callback): mixed;
}
