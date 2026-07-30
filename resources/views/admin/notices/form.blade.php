@extends('layouts.app')

@section('content')
<h2 class="pagettl">{{ isset($notice) ? 'お知らせ編集' : 'お知らせ追加' }}</h2>

<div class="panel">
    <form method="POST" action="{{ isset($notice) ? route('admin.notices.update', $notice) : route('admin.notices.store') }}">
        @csrf
        @if (isset($notice))
        @method('PUT')
        @endif

        <div class="field">
            <label for="published_at">公開日</label>
            <input id="published_at" name="published_at" type="date" value="{{ old('published_at', isset($notice) ? $notice->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        </div>

        <div class="field">
            <label for="title">タイトル</label>
            <input id="title" name="title" type="text" value="{{ old('title', $notice->title ?? '') }}" required>
        </div>

        <div class="field">
            <label for="content">本文</label>
            <textarea id="content" name="content" rows="4">{{ old('content', $notice->content ?? '') }}</textarea>
        </div>

        <div class="field">
            <label for="link">リンクURL</label>
            <input id="link" name="link" type="text" value="{{ old('link', $notice->link ?? '') }}" placeholder="manual.html">
        </div>

        <div class="field">
            <label for="pdf_link">PDFリンク</label>
            <input id="pdf_link" name="pdf_link" type="text" value="{{ old('pdf_link', $notice->pdf_link ?? '') }}" placeholder="manual.pdf">
        </div>

        <div class="field">
            <label>
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $notice->is_active ?? true) ? 'checked' : '' }}>
                公開する
            </label>
        </div>

        @if ($errors->any())
        <div class="err" style="margin-bottom:12px;">{{ $errors->first() }}</div>
        @endif

        <div class="formfoot">
            <button class="btn" type="submit">保存</button>
            <a class="btn ghost" href="{{ route('admin.notices.index') }}">戻る</a>
        </div>
    </form>
</div>
@endsection