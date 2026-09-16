<?php

namespace App\Http\Controllers;

use App\Mail\EncomiendaNotificacion;
use App\Models\Ajuste;
use App\Models\Colaborador;
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
            'colaboradores' => Colaborador::orderBy('nombre')->get(),
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
            'interesado' => ['required', 'string', 'max:255'],
            'documento_interesado' => ['required', 'string', 'max:30'],
            'obs' => ['nullable', 'string'],
            'enlace_drive' => ['nullable', 'url', 'max:2048'],
            'colaborador_cedula' => ['required', 'string', 'max:30'],
            'colaborador_nombre' => ['required', 'string', 'max:255'],
            'colaborador_correo' => ['required', 'email', 'max:255'],
        ]);

        $ajuste = Ajuste::actual();

        $encomienda = DB::transaction(function () use ($data, $ajuste) {
            $colaborador = Colaborador::firstOrCreate(
                ['cedula' => $data['colaborador_cedula']],
                ['nombre' => $data['colaborador_nombre'], 'correo' => $data['colaborador_correo']],
            );

            $fecha = new \DateTime($data['fecha']);

            $encomienda = Encomienda::create([
                'codigo' => Encomienda::generarCodigo($ajuste->prefijo, $fecha),
                'fecha' => $data['fecha'],
                'tipo' => $data['tipo'],
                'descripcion' => $data['descripcion'],
                'remitente' => $data['remitente'] ?? null,
                'guia' => $data['guia'] ?? null,
                'recibe' => $colaborador->nombre,
                'interesado' => $data['interesado'],
                'documento_interesado' => $data['documento_interesado'],
                'colaborador_id' => $colaborador->id,
                'obs' => $data['obs'] ?? null,
                'enlace_drive' => $data['enlace_drive'] ?? null,
                'estado' => 'recibida',
            ]);

            $encomienda->historial()->create([
                'estado' => 'recibida',
                'por' => $colaborador->nombre,
                'fecha' => now(),
            ]);

            return $encomienda;
        });

        if ($request->user()->role === 'recepcion') {
            return redirect()
                ->route('encomiendas.create')
                ->with('status', 'Encomienda '.$encomienda->codigo.' registrada');
        }

        return redirect()
            ->route('encomiendas.index', ['highlight' => $encomienda->id])
            ->with('status', 'Encomienda '.$encomienda->codigo.' registrada');
    }

    public function index(Request $request): View
    {
        $filtro = $request->query('filtro', 'todas');
        $buscar = trim((string) $request->query('buscar', ''));

        $query = Encomienda::with(['dependencia', 'colaborador'])->orderByDesc('fecha');

        if (in_array($filtro, ['recibida', 'en_administrativa', 'notificada', 'entregada'], true)) {
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
            'dependencias' => Dependencia::orderBy('nombre')->get(),
        ]);
    }

    public function reasignar(Request $request, Encomienda $encomienda): RedirectResponse
    {
        abort_unless($encomienda->estado === 'recibida', 422, 'La encomienda ya fue reasignada.');

        $data = $request->validate([
            'dependencia_id' => ['required', 'exists:dependencias,id'],
            'correo' => ['required', 'email', 'max:255'],
        ]);

        $ajuste = Ajuste::actual();

        DB::transaction(function () use ($request, $encomienda, $data) {
            $encomienda->update([
                'dependencia_id' => $data['dependencia_id'],
                'correo' => $data['correo'],
                'estado' => 'en_administrativa',
                'reasignada_por' => $request->user()->name,
                'reasignada_at' => now(),
            ]);

            $encomienda->historial()->create([
                'estado' => 'en_administrativa',
                'por' => $request->user()->name,
                'fecha' => now(),
            ]);
        });

        Mail::to($encomienda->correo)
            ->send(new EncomiendaNotificacion($encomienda, $ajuste->estacion));

        $encomienda->update(['estado' => 'notificada']);
        $encomienda->historial()->create([
            'estado' => 'notificada',
            'por' => 'Correo automático',
            'fecha' => now(),
        ]);

        return back()->with('status', 'Encomienda reasignada a '.$encomienda->dependencia->nombre.' y notificada por correo');
    }

    public function notificar(Request $request, Encomienda $encomienda): RedirectResponse
    {
        if ($encomienda->estado === 'notificada' && $encomienda->correo) {
            $ajuste = Ajuste::actual();
            Mail::to($encomienda->correo)
                ->send(new EncomiendaNotificacion($encomienda, $ajuste->estacion));
        }

        return back()->with('status', 'Correo reenviado');
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

        $query = Encomienda::with(['dependencia', 'colaborador'])->orderBy('fecha');

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
                'Colaborador recepción', 'Cédula colaborador', 'Interesado', 'Documento interesado', 'Dependencia',
                'WhatsApp', 'Correo', 'Enlace Drive', 'Observaciones',
                'Estado', 'Reasignada por', 'Fecha reasignación', 'Entregado a', 'Fecha entrega',
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
                        $e->colaborador?->nombre ?? $e->recibe,
                        $e->colaborador?->cedula,
                        $e->interesado,
                        $e->documento_interesado,
                        $e->dependencia?->nombre,
                        $e->whatsapp,
                        $e->correo,
                        $e->enlace_drive,
                        $e->obs,
                        ucfirst(str_replace('_', ' ', $e->estado)),
                        $e->reasignada_por,
                        $e->reasignada_at?->format('d/m/Y H:i'),
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
