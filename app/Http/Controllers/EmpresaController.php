<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Auth::user()->empresa;

        return view('empresa.index', compact('empresa'));
    }

    public function update(Request $request)
    {
        $empresa = Auth::user()->empresa;

        $validated = $request->validate([
            'nombre'               => 'required|string|max:255',
            'ruc'                  => ['nullable', 'digits:11', Rule::unique('empresas', 'ruc')->ignore($empresa->id)],
            'telefono'             => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:255',
            'razon_social'         => 'nullable|string|max:255',
            'nombre_comercial'     => 'nullable|string|max:255',
            'direccion_fiscal'     => 'nullable|string|max:255',
            'ubigeo'               => 'nullable|digits:6',
            'departamento'         => 'nullable|string|max:100',
            'provincia'            => 'nullable|string|max:100',
            'distrito'             => 'nullable|string|max:100',
            'urbanizacion'         => 'nullable|string|max:100',
            'web'                  => 'nullable|string|max:255',
            'ambiente_sunat'       => 'required|in:beta,produccion',
            'sol_user'             => 'nullable|string|max:50',
            'sol_pass'             => 'nullable|string|max:100',
            'certificado'          => 'nullable|file|max:2048|extensions:pem,pfx,p12',
            'certificado_password' => 'nullable|string|max:255',
            'logo'                 => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
            'facturacion_activa'   => 'nullable|boolean',
        ]);

        // La dirección general sigue a la fiscal
        $validated['direccion'] = $validated['direccion_fiscal'] ?? $empresa->direccion;

        // Clave SOL: solo se actualiza si escriben una nueva
        if (blank($validated['sol_pass'] ?? null)) {
            unset($validated['sol_pass']);
        }

        // Certificado digital (.pem directo, o .pfx/.p12 convertido a PEM)
        if ($request->hasFile('certificado')) {
            $validated['certificado_path'] = $this->guardarCertificado(
                $request->file('certificado'),
                (string) $request->input('certificado_password', ''),
                $empresa->id
            );
        }
        unset($validated['certificado']);

        if (blank($validated['certificado_password'] ?? null)) {
            unset($validated['certificado_password']);
        }

        // Logo
        if ($request->hasFile('logo')) {
            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['facturacion_activa'] = $request->boolean('facturacion_activa');

        // SUNAT exige mayúsculas en estos datos
        foreach (['razon_social', 'nombre_comercial', 'direccion_fiscal', 'departamento', 'provincia', 'distrito', 'urbanizacion', 'sol_user'] as $campo) {
            if (isset($validated[$campo])) {
                $validated[$campo] = mb_strtoupper($validated[$campo]);
            }
        }

        $empresa->update($validated);

        return back()->with('success', 'Información de la empresa actualizada correctamente.');
    }

    /**
     * Guarda el certificado en storage/app/sunat/certificados como PEM.
     */
    private function guardarCertificado($archivo, string $password, int $empresaId): string
    {
        $contenido = file_get_contents($archivo->getRealPath());
        $extension = strtolower($archivo->getClientOriginalExtension());

        if (in_array($extension, ['pfx', 'p12'])) {
            $certs = [];
            if (! openssl_pkcs12_read($contenido, $certs, $password)) {
                throw ValidationException::withMessages([
                    'certificado' => 'No se pudo leer el certificado .pfx/.p12. Verifica la contraseña.',
                ]);
            }

            $pem = $certs['pkey'] . $certs['cert'];
            foreach ($certs['extracerts'] ?? [] as $extra) {
                $pem .= $extra;
            }
            $contenido = $pem;
        } elseif (! str_contains($contenido, '-----BEGIN')) {
            throw ValidationException::withMessages([
                'certificado' => 'El archivo .pem no tiene un formato válido.',
            ]);
        }

        $nombre = 'certificado-' . $empresaId . '.pem';
        $directorio = config('sunat.paths.certificados');

        if (! is_dir($directorio)) {
            mkdir($directorio, 0750, true);
        }

        file_put_contents($directorio . DIRECTORY_SEPARATOR . $nombre, $contenido);

        return $nombre;
    }
}
