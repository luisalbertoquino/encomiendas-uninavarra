<?php

namespace App\Http\Controllers;

use App\Mail\EncomiendaNotificacion;
use App\Models\Ajuste;
use App\Models\Dependencia;
use App\Models\Encomienda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EncomiendaController extends Controller
{
    public function create(): View
    {
        return view('encomiendas.registrar', [
            'dependencias' => Dependencia::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'tipo' => ['required', 'string', 'max:50'],
            'descripcion' => ['required', 'string', 'max:255'],
            'remitente' => ['nullable', 'string', 'max:255'],
            'guia' => ['nullable', 'string', 'max:100'],
            'recibe' => ['required', 'string', 'max:255'],
            'interesado' => ['required', 'string', 'max:255'],
            'documento_interesado' => ['required', 'string', 'max:30'],
            'dependencia_id' => ['nullable', 'exists:dependencias,id'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
            'obs' => ['nullable', 'string'],
            'enlace_drive' => ['nullable', 'url', 'max:2048'],
        ]);

        $ajuste = Ajuste::actual();

        $encomienda = DB::transaction(function () use ($data, $ajuste) {
            $fecha = new \DateTime($data['fecha']);
            $data['codigo'] = Encomienda::generarCodigo($ajuste->prefijo, $fecha);
            $data['estado'] = ($data['correo'] ?? null) ? 'notificada' : 'recibida';

            $encomienda = Encomienda::create($data);

            $encomienda->historial()->create([
                'estado' => 'recibida',
                'por' => $data['recibe'],
                'fecha' => now(),
            ]);

            if ($encomienda->estado === 'notificada') {
                $encomienda->historial()->create([
                    'estado' => 'notificada',
                    'por' => 'Correo automático',
                    'fecha' => now(),
                ]);
            }

            return $encomienda;
        });

        if ($encomienda->correo) {
            Mail::to($encomienda->correo)
                ->send(new EncomiendaNotificacion($encomienda, $ajuste->estacion));
        }

        return redirect()
            ->route('encomiendas.index', ['highlight' => $encomienda->id])
            ->with('status', 'Encomienda '.$encomienda->codigo.' registrada'.($encomienda->correo ? ' y notificada por correo' : ''));
    }

    public function index(Request $request): View
    {
        $filtro = $request->query('filtro', 'todas');
        $buscar = trim((string) $request->query('buscar', ''));

        $query = Encomienda::with('dependencia')->orderByDesc('fecha');

        if (in_array($filtro, ['recibida', 'notificada', 'entregada'], true)) {
            $query->where('estado', $filtro);
        }

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('interesado', 'like', "%{$buscar}%")
                    ->orWhere('remitente', 'like', "%{$buscar}%")
                    ->orWhere('recibe', 'like', "%{$buscar}%")
                    ->orWhereHas('dependencia', fn ($d) => $d->where('nombre', 'like', "%{$buscar}%"));
            });
        }

        return view('encomiendas.bandeja', [
            'encomiendas' => $query->paginate(20)->withQueryString(),
            'filtro' => $filtro,
            'buscar' => $buscar,
            'ajuste' => Ajuste::actual(),
            'highlight' => $request->query('highlight'),
        ]);
    }

    public function notificar(Request $request, Encomienda $encomienda): RedirectResponse
    {
        if ($encomienda->estado === 'recibida') {
            $encomienda->update(['estado' => 'notificada']);
            $encomienda->historial()->create([
                'estado' => 'notificada',
                'por' => $request->user()->name,
                'fecha' => now(),
            ]);

            if ($encomienda->correo) {
                $ajuste = Ajuste::actual();
                Mail::to($encomienda->correo)
                    ->send(new EncomiendaNotificacion($encomienda, $ajuste->estacion));
            }
        }

        return back()->with('status', 'Marcada como notificada');
    }

    public function entregar(Request $request, Encomienda $encomienda): RedirectResponse
    {
        $data = $request->validate([
            'entregado_a' => ['nullable', 'string', 'max:255'],
        ]);

        $entregadoA = trim($data['entregado_a'] ?? '') ?: $encomienda->interesado;

        $encomienda->update([
            'estado' => 'entregada',
            'entregado_a' => $entregadoA,
            'fecha_entrega' => now(),
        ]);

        $encomienda->historial()->create([
            'estado' => 'entregada',
            'por' => $entregadoA,
            'fecha' => now(),
        ]);

        return back()->with('status', 'Encomienda entregada');
    }

    public function destroy(Encomienda $encomienda): RedirectResponse
    {
        $encomienda->delete();

        return back()->with('status', 'Eliminada');
    }

    public function estacion(): View
    {
        return view('encomiendas.estacion', [
            'ajuste' => Ajuste::actual(),
        ]);
    }

    public function ajustes(): View
    {
        $ajuste = Ajuste::actual();

        return view('encomiendas.ajustes', [
            'ajuste' => $ajuste,
            'total' => Encomienda::count(),
        ]);
    }

    public function exportar(Request $request): StreamedResponse
    {
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');

        $query = Encomienda::with('dependencia')->orderBy('fecha');

        if ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }

        $filename = 'historico-encomiendas-uninavarra-'.now()->format('Y-m-d').'.csv';

        $callback = function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM para que Excel detecte UTF-8

            fputcsv($out, [
                'Código', 'Fecha recepción', 'Tipo', 'Descripción', 'Remitente', 'Guía',
                'Recibió', 'Interesado', 'Documento interesado', 'Dependencia',
                'WhatsApp', 'Correo', 'Enlace Drive', 'Observaciones',
                'Estado', 'Entregado a', 'Fecha entrega',
            ], ';');

            $query->chunk(200, function ($encomiendas) use ($out) {
                foreach ($encomiendas as $e) {
                    fputcsv($out, [
                        $e->codigo,
                        $e->fecha->format('d/m/Y H:i'),
                        $e->tipo,
                        $e->descripcion,
                        $e->remitente,
                        $e->guia,
                        $e->recibe,
                        $e->interesado,
                        $e->documento_interesado,
                        $e->dependencia?->nombre,
                        $e->whatsapp,
                        $e->correo,
                        $e->enlace_drive,
                        $e->obs,
                        ucfirst($e->estado),
                        $e->entregado_a,
                        $e->fecha_entrega?->format('d/m/Y H:i'),
                    ], ';');
                }
            });

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function ajustesUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'estacion' => ['nullable', 'string', 'max:255'],
            'prefijo' => ['nullable', 'string', 'max:6'],
            'url' => ['nullable', 'url', 'max:255'],
        ]);

        $ajuste = Ajuste::actual();
        $ajuste->update([
            'estacion' => trim($data['estacion'] ?? '') ?: 'Recepción principal',
            'prefijo' => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $data['prefijo'] ?? '')) ?: 'UNV',
            'url' => trim($data['url'] ?? '') ?: null,
        ]);

        return back()->with('status', 'Ajustes guardados');
    }
}
