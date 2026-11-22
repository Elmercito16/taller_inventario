<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Tenant;

class Empresa extends Tenant
{
    use HasFactory;

    /**
     * Nombre de la tabla
     */
    protected $table = 'empresas';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'ruc',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'direccion_fiscal',
        'ubigeo',
        'departamento',
        'provincia',
        'distrito',
        'urbanizacion',
        'codigo_pais',
        'telefono',
        'email',
        'web',
        'logo',
        // Credenciales SUNAT
        'sol_user',
        'sol_pass',
        // Certificado Digital
        'certificado_path',
        'certificado_password',
        // Configuración de facturación
        'facturacion_activa',
        'ambiente_sunat', // 'beta' o 'produccion'
    ];

    /**
     * Casts de atributos
     */
    protected $casts = [
        'facturacion_activa' => 'boolean',
    ];

    /**
     * Ocultar campos sensibles en JSON
     */
    protected $hidden = [
        'sol_user',
        'sol_pass',
        'certificado_password',
    ];

    /**
     * MÉTODO REQUERIDO POR SPATIE MULTITENANCY V4
     * Retorna el nombre de la columna que identifica al tenant
     */
    public function getTenantKeyName(): string
    {
        return 'empresa_id';
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'empresa_id');
    }

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'empresa_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'empresa_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'empresa_id');
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'empresa_id');
    }

    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'empresa_id');
    }

    public function series()
    {
        return $this->hasMany(Serie::class, 'empresa_id');
    }

    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class, 'empresa_id');
    }

    // ==========================================
    // MÉTODOS PARA FACTURACIÓN ELECTRÓNICA
    // ==========================================

    /**
     * Verifica si tiene facturación electrónica activa
     */
    public function tieneFacturacionActiva()
    {
        return $this->facturacion_activa === true;
    }

    /**
     * Verifica si está en ambiente de producción
     */
    public function esProduccion()
    {
        return $this->ambiente_sunat === 'produccion';
    }

    /**
     * Verifica si está en ambiente Beta (pruebas)
     */
    public function esBeta()
    {
        // Si no está configurado, se asume Beta (pruebas) por seguridad.
        return $this->ambiente_sunat !== 'produccion';
    }

    /**
     * Obtiene la serie activa para un tipo de comprobante
     * @param string $tipoComprobante '01' = Factura, '03' = Boleta, etc.
     * @return Serie|null
     */
    public function getSerieActiva($tipoComprobante)
    {
        return $this->series()
                    ->where('tipo_comprobante', $tipoComprobante)
                    ->where('activo', true)
                    ->first();
    }

    /**
     * Obtiene el siguiente número de comprobante
     * @param string $tipoComprobante
     * @return string|null Ejemplo: "F001-00000001"
     */
    public function getSiguienteNumeroComprobante($tipoComprobante)
    {
        $serie = $this->getSerieActiva($tipoComprobante);
        
        if (!$serie) {
            return null;
        }

        $siguienteCorrelativo = $serie->correlativo_actual + 1;
        
        return $serie->serie . '-' . str_pad($siguienteCorrelativo, 8, '0', STR_PAD_LEFT);
    }

    // ==========================================
    // CERTIFICADO DIGITAL
    // ==========================================

    /**
     * Obtiene la ruta completa del certificado digital
     */
    public function getRutaCertificado(): ?string
    {
        if (empty($this->certificado_path)) {
            return null;
        }

        $archivo = basename($this->certificado_path);
        $ruta = rtrim(config('sunat.paths.certificados'), '/\\') . DIRECTORY_SEPARATOR . $archivo;

        // Compatibilidad: versiones anteriores guardaban en storage/app/private/sunat/certificados
        $rutaAntigua = storage_path('app/private/sunat/certificados/' . $archivo);
        if (! file_exists($ruta) && file_exists($rutaAntigua)) {
            return $rutaAntigua;
        }

        return $ruta;
    }

    /**
     * Verifica si tiene certificado configurado
     */
    public function tieneCertificado()
    {
        if (empty($this->certificado_path)) {
            return false;
        }

        $ruta = $this->getRutaCertificado();
        
        return file_exists($ruta);
    }

    /**
     * Obtiene la contraseña del certificado
     */
    public function getPasswordCertificado()
{
    // En ambiente Beta el certificado no tiene contraseña
    if ($this->esBeta()) {
        return '';
    }
    
    return $this->certificado_password ?? '';
}


    // ==========================================
    // CREDENCIALES SUNAT (SOL)
    // ==========================================

    /**
     * Verifica si tiene credenciales SOL configuradas
     */
    public function tieneCredencialesSol()
    {
        return !empty($this->sol_user) && !empty($this->sol_pass);
    }

    /**
     * Usuario SOL SIN el RUC adelante (Greenter concatena RUC + usuario).
     * En Beta se usan las credenciales públicas de pruebas de SUNAT.
     */
    public function getSolUser(): ?string
    {
        if ($this->esBeta()) {
            return config('sunat.beta.sol_user', 'MODDATOS');
        }

        $usuario = trim((string) $this->sol_user);
        if ($usuario === '') {
            return null;
        }

        // Si lo guardaron como RUC+USUARIO, quitamos el RUC.
        if ($this->ruc && str_starts_with($usuario, $this->ruc)) {
            $usuario = substr($usuario, strlen($this->ruc));
        }

        return $usuario;
    }

    public function getSolPass(): ?string
    {
        if ($this->esBeta()) {
            return config('sunat.beta.sol_pass', 'moddatos');
        }

        return $this->sol_pass ?: null;
    }

    /**
 * Obtiene las credenciales completas para SUNAT
 * Usado por Greenter
 */
    public function getCredencialesSunat(): array
    {
        return [
            'ruc' => $this->ruc,
            'usuario' => $this->getSolUser(),
            'clave' => $this->getSolPass(),
            'certificado' => $this->getRutaCertificado(),
            'password_certificado' => $this->getPasswordCertificado(),
        ];
    }

    // ==========================================
    // VALIDACIONES
    // ==========================================

    /**
     * Verifica si tiene los datos mínimos para facturar
     */
    public function tieneDatosCompletosParaFacturar()
    {
        // Datos básicos
        if (empty($this->ruc) || empty($this->razon_social)) {
            return false;
        }

        // Dirección fiscal
        if (empty($this->direccion_fiscal) || empty($this->ubigeo)) {
            return false;
        }

        // Facturación activa
        if (!$this->facturacion_activa) {
            return false;
        }

        // En producción necesita certificado y credenciales SOL
        if ($this->esProduccion()) {
            if (!$this->tieneCertificado() || !$this->tieneCredencialesSol()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtiene los errores de configuración para facturar
     */
    public function getErroresConfiguracion()
    {
        $errores = [];

        if (empty($this->ruc)) {
            $errores[] = 'Falta configurar el RUC';
        }

        if (empty($this->razon_social)) {
            $errores[] = 'Falta configurar la Razón Social';
        }

        if (empty($this->direccion_fiscal)) {
            $errores[] = 'Falta configurar la Dirección Fiscal';
        }

        if (empty($this->ubigeo)) {
            $errores[] = 'Falta configurar el Ubigeo';
        }

        if (!$this->facturacion_activa) {
            $errores[] = 'La facturación electrónica está desactivada';
        }

        if ($this->esProduccion()) {
            if (!$this->tieneCertificado()) {
                $errores[] = 'Falta el certificado digital para producción';
            }

            if (!$this->tieneCredencialesSol()) {
                $errores[] = 'Faltan las credenciales SOL para producción';
            }
        }

        return $errores;
    }

    // ==========================================
    // ACCESORIOS (GETTERS)
    // ==========================================

    /**
     * Nombre completo de la empresa (razón social o nombre)
     */
    public function getNombreCompletoAttribute()
    {
        return $this->razon_social ?? $this->nombre;
    }

    /**
     * Dirección completa formateada
     */
    public function getDireccionCompletaAttribute()
    {
        $direccion = $this->direccion_fiscal ?? $this->direccion;
        $distrito = $this->distrito;
        $provincia = $this->provincia;
        $departamento = $this->departamento;

        return trim("{$direccion} - {$distrito}, {$provincia}, {$departamento}");
    }

    /**
     * URL del logo (si existe)
     */
    public function getLogoUrlAttribute()
    {
        return ! empty($this->logo) ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Logo embebido como data URI (base64) para PDFs y tickets impresos.
     * Dompdf no carga bien imágenes por URL ni WEBP: el WEBP se convierte a PNG.
     */
    public function logoDataUri(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        $ruta = \Illuminate\Support\Facades\Storage::disk('public')->path($this->logo);
        if (! is_file($ruta)) {
            return null;
        }

        try {
            $mime = mime_content_type($ruta) ?: 'image/png';

            if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
                $imagen = imagecreatefromwebp($ruta);
                imagesavealpha($imagen, true);
                ob_start();
                imagepng($imagen);
                $contenido = ob_get_clean();
                imagedestroy($imagen);
                $mime = 'image/png';
            } else {
                $contenido = file_get_contents($ruta);
            }

            return 'data:' . $mime . ';base64,' . base64_encode($contenido);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    
}
