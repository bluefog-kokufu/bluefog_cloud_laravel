@extends('layouts.app')

@section('content')
<h2 class="pagettl">お知らせ管理</h2>

<div class="panel">
    <div class="toolbar" style="margin-bottom:12px; justify-content:space-between;">
        <a class="btn accent" href="{{ route('admin.notices.create') }}">新規追加</a>
        <a class="btn ghost" href="{{ route('dashboard') }}">ダッシュボードへ戻る</a>
    </div>

    @if (session('status'))
    <div class="card" style="background:#e8f8ee; color:#1d7a45;">{{ session('status') }}</div>
    @endif

    <table class="list">
        <thead>
            <tr>
                <th>公開日</th>
                <th>タイトル</th>
                <th>状態</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($notices as $notice)
            <tr>
                <td>{{ $notice->published_at->format('Y.m.d') }}</td>
                <td>{{ $notice->title }}</td>
                <td>{{ $notice->is_active ? '公開中' : '非公開' }}</td>
                <td>
                    <a href="{{ route('admin.notices.edit', $notice) }}" class="btn small">編集</a>
                    <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" style="display:inline;" onsubmit="return confirm('削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger small" type="submit">削除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection