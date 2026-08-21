<?php

if (!function_exists('terbilang')) {
    /**
     * Convert number to Indonesian words
     *
     * @param int $angka
     * @return string
     */
    function terbilang($angka)
    {
        $angka = abs($angka);
        $huruf = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
        ];
        
        $temp = '';
        
        if ($angka < 12) {
            $temp = ' ' . $huruf[$angka];
        } elseif ($angka < 20) {
            $temp = terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $temp = terbilang($angka / 10) . ' puluh' . terbilang($angka % 10);
        } elseif ($angka < 200) {
            $temp = ' seratus' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $temp = terbilang($angka / 100) . ' ratus' . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' seribu' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = terbilang($angka / 1000) . ' ribu' . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $temp = terbilang($angka / 1000000) . ' juta' . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $temp = terbilang($angka / 1000000000) . ' milyar' . terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $temp = terbilang($angka / 1000000000000) . ' trilyun' . terbilang(fmod($angka, 1000000000000));
        }
        
        return trim($temp);
    }
}

