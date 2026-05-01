<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Clientes;

use App\Exceptions\Clientes\DuplicateIdentificationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clientes\QuickCreateCustomerRequest;
use App\Services\Clientes\Contracts\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerSearchController extends Controller
{
    public function __construct(
        private readonly CustomerServiceInterface $customerService,
    ) {}

    public function search(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');

        $results = $this->customerService->search($query);

        return response()->json($results);
    }

    public function quickCreate(QuickCreateCustomerRequest $request): JsonResponse
    {
        try {
            $customer = $this->customerService->quickCreate($request->validated());
        } catch (DuplicateIdentificationException $e) {
            return response()->json(
                ['errors' => ['identificacion_duplicada' => [$e->getMessage()]]],
                422,
            );
        }

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'identification' => $customer->identification,
            'is_frequent' => $customer->is_frequent,
        ], 201);
    }
}
