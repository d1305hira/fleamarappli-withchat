@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/top.css') }}">
@endsection

@section('content')
<div class="page-container">
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'recommended' ? 'active' : '' }}" href="{{ route('top', ['tab' => 'recommended']) }}">おすすめ</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'mylist' ? 'active' : '' }}" href="{{ route('top', ['tab' => 'mylist']) }}">マイリスト</a>
  </li>
</ul>

{{-- 検索結果がある場合はそれだけ表示 --}}
@if (!empty($keyword))
  <h2 class="mb-4">「{{ $keyword }}」の検索結果</h2>

  <div class="items">
    @forelse ($items as $item)
        <div class="card item-card">
          <a href="{{ route('item.show', $item->id) }}">
            <img src="{{ Storage::url($item->image) }}" class="card-img-top" alt="{{ $item->name }}">
            @if($item->is_sold)
  <span class="badge bg-danger">sold</span>
@endif
          </a>
          <div class="card-body">
            <h5 class="card-title">{{ $item->name }}</h5>
          </div>
        </div>
    @empty
      <p>該当する商品はありません。</p>
    @endforelse
  </div>

@else
  {{-- 通常表示（検索していないとき） --}}
  @if ($tab === 'mylist')
    @auth
      <div class="items justify-content-center">
        @forelse ($likedItems as $item)
            <div class="card item-card">
              <a href="{{ route('item.show', $item->id) }}">
                <img src="{{ Storage::url($item->image) }}" class="card-img-top" alt="{{ $item->name }}">
                @if ($item->isSold())
                  <span class="sold-label">sold</span>
                @endif
              </a>
              <div class="card-body">
                <h5 class="card-title">{{ $item->name }}</h5>
              </div>
            </div>
        @empty
          <p>まだ「いいね」した商品はありません。</p>
        @endforelse
      </div>
    @else
      <p>マイリストを表示するにはログインが必要です。</p>
    @endauth

  @elseif ($tab === 'recommended')
    <div class="items">
      @foreach ($items as $item)
          <div class="card item-card">
            <a href="{{ route('item.show', $item->id) }}">
              <img src="{{ Storage::url($item->image) }}" class="card-img-top" alt="{{ $item->name }}">
              @if ($item->isSold())
                <span class="sold-label">sold</span>
              @endif
            </a>
            <div class="card-body">
              <h5 class="card-title">{{ $item->name }}</h5>
            </div>
          </div>
      @endforeach
    </div>
  @endif
@endif
</div>
@endsection