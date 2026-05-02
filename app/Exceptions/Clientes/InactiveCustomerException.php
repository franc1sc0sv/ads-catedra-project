<?php

declare(strict_types=1);

namespace App\Exceptions\Clientes;

use App\Models\Customer;
use RuntimeException;

class InactiveCustomerException extends RuntimeException
{
    public function __construct(
        public readonly Customer $customer,
        string $message = 'La identificación pertenece a un cliente inactivo.',
    ) {
        parent::__construct($message);
    }
}
