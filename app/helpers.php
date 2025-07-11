<?php

function getTanggalPengiriman($created_at)
{
    $day = \Carbon\Carbon::parse($created_at)->dayOfWeek;
    if ($day === 0 || $day === 2) return now()->next('wednesday')->translatedFormat('l, d F Y');
    if ($day === 3 || $day === 4) return now()->next('friday')->translatedFormat('l, d F Y');
    return now()->next('sunday')->translatedFormat('l, d F Y');
}
