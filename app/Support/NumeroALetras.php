<?php

namespace App\Support;

/**
 * Convierte montos a letras en español (formato SUNAT):
 *   1250.5 => "MIL DOSCIENTOS CINCUENTA CON 50/100 SOLES"
 *
 * No depende de la extensión intl.
 */
class NumeroALetras
{
    private const UNIDADES = [
        '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
        'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE',
        'VEINTE', 'VEINTIUNO', 'VEINTIDÓS', 'VEINTITRÉS', 'VEINTICUATRO', 'VEINTICINCO', 'VEINTISÉIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE',
    ];

    private const DECENAS = ['', '', '', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];

    private const CENTENAS = [
        '', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
        'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS',
    ];

    public static function monto(float $monto, string $moneda = 'SOLES'): string
    {
        $monto = round($monto, 2);
        $entero = (int) floor($monto);
        $centimos = (int) round(($monto - $entero) * 100);

        if ($centimos === 100) {
            $entero++;
            $centimos = 0;
        }

        $letras = $entero === 0 ? 'CERO' : self::convertir($entero);

        return trim($letras) . ' CON ' . str_pad((string) $centimos, 2, '0', STR_PAD_LEFT) . '/100 ' . $moneda;
    }

    public static function convertir(int $numero): string
    {
        if ($numero === 0) {
            return 'CERO';
        }

        $partes = [];

        $millones = intdiv($numero, 1_000_000);
        $numero %= 1_000_000;
        if ($millones > 0) {
            $partes[] = $millones === 1 ? 'UN MILLÓN' : self::apocopar(self::menorAMil($millones)) . ' MILLONES';
        }

        $miles = intdiv($numero, 1000);
        $numero %= 1000;
        if ($miles > 0) {
            $partes[] = $miles === 1 ? 'MIL' : self::apocopar(self::menorAMil($miles)) . ' MIL';
        }

        if ($numero > 0) {
            $partes[] = self::menorAMil($numero);
        }

        return implode(' ', $partes);
    }

    private static function menorAMil(int $n): string
    {
        if ($n === 100) {
            return 'CIEN';
        }

        $c = intdiv($n, 100);
        $resto = $n % 100;
        $texto = self::CENTENAS[$c];

        if ($resto > 0) {
            $texto .= ($texto ? ' ' : '') . self::menorACien($resto);
        }

        return $texto;
    }

    private static function menorACien(int $n): string
    {
        if ($n < 30) {
            return self::UNIDADES[$n];
        }

        $d = intdiv($n, 10);
        $u = $n % 10;

        return self::DECENAS[$d] . ($u ? ' Y ' . self::UNIDADES[$u] : '');
    }

    /**
     * "UNO" => "UN" antes de MIL/MILLONES (ej. "VEINTIÚN MIL", "TREINTA Y UN MIL").
     */
    private static function apocopar(string $texto): string
    {
        if (str_ends_with($texto, 'VEINTIUNO')) {
            return substr($texto, 0, -strlen('VEINTIUNO')) . 'VEINTIÚN';
        }

        if (str_ends_with($texto, 'UNO')) {
            return substr($texto, 0, -3) . 'UN';
        }

        return $texto;
    }
}
