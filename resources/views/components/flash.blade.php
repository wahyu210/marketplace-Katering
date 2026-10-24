@props(['inline' => false])
@if (session('status'))
    <div class="flash" role="status" style="margin-bottom:24px">
        <span>{{ session('status') }}</span>
    </div>
@endif
@if ($errors->any() && ! $inline)
    <div class="flash is-error" role="alert" style="margin-bottom:24px">
        <div>
            <strong>Periksa kembali isian Anda.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
