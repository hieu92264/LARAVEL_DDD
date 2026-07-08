<?php

declare(strict_types=1);

namespace App\Core\Domain\Exceptions;

use RuntimeException;

/**
 * Class DomainException
 * @package App\Core\Domain\Exceptions
 * @method string errorCode() Get the error code associated with the exception.
 * goal: phan tach loi nghiep vu va loi the thong
 * bat buoc cac loi nghiep vu ke thua tu class nay phai khai bao 1 ma loi
 * khong khai bao ma loi o day vi httpstatus code thuoc ve tang Presentation, khong thuoc ve tang Domain
 */
abstract class DomainException extends RuntimeException
{
    abstract public function errorCode(): string;
}
