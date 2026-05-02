<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Enums\PrescriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PharmacistController extends Controller
{
    /**
     * Dashboard con vista previa de pendientes.
     */
    public function index()
    {
    $prescriptions = Prescription::with(['medication'])
        ->where('status', PrescriptionStatus::PENDING)
        ->latest('issued_at')
        ->get()
        ->groupBy('prescription_number'); // ESTO ES LO QUE CREA LA ESTRUCTURA $number => $items

    return view('pharmacist.dashboard.index', compact('prescriptions'));
    }

    /**
     * Cola de recetas completa.
     */
    public function queue()
    {
        $prescriptions = Prescription::with(['medication'])
            ->where('status', PrescriptionStatus::PENDING)
            ->latest('issued_at')
            ->get()
            ->groupBy('prescription_number');

        return view('pharmacist.dashboard.queue', compact('prescriptions'));
    }

    /**
     * Valida todos los medicamentos de una receta por su número.
     */
    public function validate(string $prescriptionNumber): RedirectResponse
    {
        $affected = Prescription::where('prescription_number', $prescriptionNumber)
            ->where('status', PrescriptionStatus::PENDING)
            ->update([
                'status' => PrescriptionStatus::VALIDATED,
                'validated_at' => now(),
                'pharmacist_id' => auth()->id(),
            ]);

        if ($affected > 0) {
            return redirect()->route('pharmacist.queue')
                ->with('success', "La receta #{$prescriptionNumber} ha sido validada.");
        }

        return redirect()->back()->with('error', 'No se pudo validar la receta.');
    }

    /**
     * Historial de recetas despachadas.
     */
    public function history()
    {
        $history = Prescription::with(['medication', 'pharmacist'])
            ->where('status', PrescriptionStatus::VALIDATED)
            ->latest('validated_at')
            ->get()
            ->groupBy('prescription_number');

        return view('pharmacist.dashboard.history', compact('history'));
    }
}