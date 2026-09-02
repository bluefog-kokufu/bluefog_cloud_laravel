@if ($errors->any())
<div class="card" style="background:#fff0f0; color:#b22; margin-bottom:12px;">
    <ul style="margin:0; padding-left:18px;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
