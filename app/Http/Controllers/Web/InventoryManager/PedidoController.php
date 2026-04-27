<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\InventoryManager;

use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Proveedores\CancelPedidoRequest;
use App\Http\Requests\Proveedores\RecibirPedidoRequest;
use App\Http\Requests\Proveedores\StorePedidoRequest;
use App\Models\Medication;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\Proveedores\Contracts\PedidoServiceInterface;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PedidoController extends Controller
{
    public function __construct(
        private readonly PedidoServiceInterface $pedidoService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->query('status') ?: null,
            'supplier_id' => $request->query('supplier_id') ?: null,
            'from' => $request->query('from') ?: null,
            'to' => $request->query('to') ?: null,
        ];

        $orders = $this->pedidoService->list(array_filter($filters, fn ($v) => $v !== null && $v !== ''));

        return view('inventory-manager.pedidos.index', [
            'orders' => $orders,
            'filters' => $filters,
            'statuses' => PurchaseOrderStatus::cases(),
            'suppliers' => Supplier::query()->orderBy('company_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('inventory-manager.pedidos.create', [
            'suppliers' => Supplier::query()
                ->where('is_active', true)
                ->orderBy('company_name')
                ->get(),
            'medications' => Medication::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price']),
        ]);
    }

    public function store(StorePedidoRequest $request): RedirectResponse
    {
        $order = $this->pedidoService->create($request->validated(), (int) $request->user()->id);

        return redirect()
            ->route('inventory-manager.pedidos.show', $order)
            ->with('status', 'Pedido creado correctamente.');
    }

    public function show(PurchaseOrder $pedido): View
    {
        $pedido->load(['supplier', 'requestedBy', 'receivedBy', 'items.medication']);

        return view('inventory-manager.pedidos.show', [
            'order' => $pedido,
        ]);
    }

    public function send(PurchaseOrder $pedido): RedirectResponse
    {
        try {
            $this->pedidoService->markShipped($pedido);
        } catch (DomainException $e) {
            return redirect()
                ->route('inventory-manager.pedidos.show', $pedido)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('inventory-manager.pedidos.show', $pedido)
            ->with('status', 'Pedido marcado como Enviado.');
    }

    public function cancel(CancelPedidoRequest $request, PurchaseOrder $pedido): RedirectResponse
    {
        try {
            $this->pedidoService->cancel($pedido, (string) $request->validated('reason'));
        } catch (DomainException $e) {
            return redirect()
                ->route('inventory-manager.pedidos.show', $pedido)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('inventory-manager.pedidos.show', $pedido)
            ->with('status', 'Pedido cancelado.');
    }

    public function recibirForm(PurchaseOrder $pedido): View|RedirectResponse
    {
        if (! in_array($pedido->status, [PurchaseOrderStatus::REQUESTED, PurchaseOrderStatus::SHIPPED], true)) {
            return redirect()
                ->route('inventory-manager.pedidos.show', $pedido)
                ->with('error', 'Solo se pueden recibir pedidos en estado Solicitado o Enviado.');
        }

        $pedido->load(['supplier', 'items.medication']);

        return view('inventory-manager.pedidos.recibir', [
            'order' => $pedido,
        ]);
    }

    public function recibir(RecibirPedidoRequest $request, PurchaseOrder $pedido): RedirectResponse
    {
        try {
            $this->pedidoService->receive(
                $pedido,
                (array) $request->validated('items'),
                (int) $request->user()->id,
            );
        } catch (DomainException $e) {
            return redirect()
                ->route('inventory-manager.pedidos.show', $pedido)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('inventory-manager.pedidos.show', $pedido)
            ->with('status', 'Pedido recibido. Stock actualizado.');
    }
}
