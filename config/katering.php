<?php

return [
    // PPN applied on every invoice.
    'tax_rate' => (float) env('KATERING_TAX_RATE', 0.11),

    // Orders must be placed at least this many days before delivery.
    'min_lead_days' => (int) env('KATERING_MIN_LEAD_DAYS', 1),

    // Invoice due N days after issue.
    'invoice_due_days' => (int) env('KATERING_INVOICE_DUE_DAYS', 7),

    'cities' => [
        'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara',
        'Bekasi', 'Bogor', 'Depok', 'Tangerang', 'Tangerang Selatan',
        'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Medan', 'Makassar', 'Denpasar',
    ],

    'delivery_slots' => ['10:30', '11:00', '11:30', '12:00', '12:30'],
];
