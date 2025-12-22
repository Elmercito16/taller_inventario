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
    return $this->ambiente_sunat === 'beta';
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
    public function getRutaCertificado()
{
    if (empty($this->certificado_path)) {
        return null;
    }
    
    return storage_path('app/certificados/' . $this->certificado_path);
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
     * Obtiene el usuario SOL (RUC + usuario)
     */
    public function getSolUser()
{
    // En ambiente Beta no se necesita usuario SOL
    if ($this->esBeta()) {
        return null;
    }
    
    // Si no tiene sol_user configurado
    if (!isset($this->sol_user) || empty($this->sol_user)) {
        return null;
    }

    // Formato: RUC + USUARIO (ej: 20123456789MODDATOS)
    if (strpos($this->sol_user, $this->ruc) === 0) {
        return $this->sol_user;
    }

    return $this->ruc . $this->sol_user;
}

    /**
     * Obtiene la contraseña SOL
     */
    public function getSolPass()
{
    // En ambiente Beta no se necesita contraseña SOL
    if ($this->esBeta()) {
        return null;
    }
    
    return $this->sol_pass ?? null;
}

    /**
 * Obtiene las credenciales completas para SUNAT
 * Usado por Greenter
 */
     public function getCredencialesSunat()
{
    $usuario = null;
    $clave = null;
    
    // Solo obtener credenciales si NO es ambiente Beta
    if (!$this->esBeta()) {
        $usuario = $this->getSolUser();
        $clave = $this->getSolPass();
    }
    
    return [
        'ruc' => $this->ruc,
        'usuario' => $usuario,
        'clave' => $clave,
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

        // Debe tener al menos una serie configurada
        if (!$this->series()->where('activo', true)->exists()) {
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

        if (!$this->series()->where('activo', true)->exists()) {
            $errores[] = 'No hay series de comprobantes configuradas';
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
        // TODO: Implementar manejo de logos
        return null;
    }

    
}
