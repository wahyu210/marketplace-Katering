@props(['status'])
<span {{ $attributes->class(['tag', 'tag-'.$status->tone()]) }}>{{ $status->label() }}</span>
