@props(['order'])
@php
    $flow = [\App\Enums\OrderStatus::Pending, \App\Enums\OrderStatus::Confirmed, \App\Enums\OrderStatus::Delivered];
    $cancelled = $order->status === \App\Enums\OrderStatus::Cancelled;
    $reached = $cancelled ? -1 : array_search($order->status, $flow, true);
@endphp
<ol class="timeline" aria-label="Progres pesanan">
    @foreach (['Dipesan', 'Dikonfirmasi katering', 'Terkirim'] as $i => $label)
        <li @class(['is-done' => $i <= $reached])>{{ $label }}</li>
    @endforeach
    @if ($cancelled)
        <li class="is-cancel">Dibatalkan</li>
    @endif
</ol>
