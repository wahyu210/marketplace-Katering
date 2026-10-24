<?php

use Illuminate\Support\Carbon;

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $amount): string
    {
        return 'Rp'.number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('tanggal')) {
    /** Indonesian long date, e.g. "Jumat, 24 Oktober 2026". */
    function tanggal(DateTimeInterface|string|null $date, bool $withDay = false): string
    {
        if (! $date) {
            return '-';
        }

        $c = Carbon::parse($date)->locale('id');

        return $c->translatedFormat($withDay ? 'l, j F Y' : 'j F Y');
    }
}
