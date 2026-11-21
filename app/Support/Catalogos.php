<?php

namespace App\Support;

/**
 * Catálogos SUNAT y del sistema, usados en formularios, ventas y facturación.
 */
class Catalogos
{
    /**
     * Unidades de medida (Catálogo 03 SUNAT - UN/ECE Rec 20).
     * [código => [nombre, abreviatura, permite decimales]]
     */
    public const UNIDADES = [
        'NIU' => ['Unidad', 'und', false],
        'ZZ'  => ['Servicio', 'serv', false],
        'KGM' => ['Kilogramo', 'kg', true],
        'GRM' => ['Gramo', 'g', true],
        'LTR' => ['Litro', 'L', true],
        'MLT' => ['Mililitro', 'ml', true],
        'GLL' => ['Galón', 'gal', true],
        'MTR' => ['Metro', 'm', true],
        'MTK' => ['Metro cuadrado', 'm²', true],
        'BX'  => ['Caja', 'caja', false],
        'PK'  => ['Paquete', 'paq', false],
        'BG'  => ['Bolsa', 'bolsa', false],
        'BO'  => ['Botella', 'bot', false],
        'DZN' => ['Docena', 'doc', false],
        'PR'  => ['Par', 'par', false],
        'SET' => ['Juego / Kit', 'jgo', false],
        'RO'  => ['Rollo', 'rollo', false],
    ];

    /**
     * Afectación del IGV (Catálogo 07 SUNAT, operaciones onerosas).
     */
    public const AFECTACIONES_IGV = [
        '10' => 'Gravado (con IGV 18%)',
        '20' => 'Exonerado',
        '30' => 'Inafecto',
    ];

    public const TIPOS_ITEM = [
        'producto' => 'Producto (controla stock)',
        'servicio' => 'Servicio (sin stock)',
    ];

    public static function nombreUnidad(?string $codigo): string
    {
        return self::UNIDADES[$codigo][0] ?? 'Unidad';
    }

    public static function abreviaturaUnidad(?string $codigo): string
    {
        return self::UNIDADES[$codigo][1] ?? 'und';
    }

    public static function unidadPermiteDecimales(?string $codigo): bool
    {
        return self::UNIDADES[$codigo][2] ?? false;
    }

    /**
     * Formatea una cantidad sin ceros sobrantes: 3.000 => "3", 1.250 => "1.25".
     */
    public static function cantidad($valor): string
    {
        $texto = number_format((float) $valor, 3, '.', '');

        return rtrim(rtrim($texto, '0'), '.');
    }
}
