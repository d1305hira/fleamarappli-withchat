@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
  <div class="container py-4">

    <!-- ユーザー情報 -->
    <div class="profile-header d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center gap-3">
    @if($user->image)
        <img src="{{ asset('storage/' . $user->image) }}" class="profile-image">
    @else
        <img src="{{ asset('images/default-profile.png') }}" class="profile-image">
    @endif

    <div class="d-flex flex-column">
        <h2 class="mb-1">{{ $user->name }}</h2>

        @if($ratingCount > 0)

            <div class="stars">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= round($averageRating))
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
        @endif
    </div>
</div>
      <a href="{{ route('profile.edit') }}" class="profile-edit-link">プロフィールを編集</a>
  </div>

    <!-- タブ切り替え -->
    <div class="tab-bar mb-4">
      <button class="tab-button {{ $tab == 'listed' ? 'active' : '' }}" onclick="location.href='?tab=listed'">出品した商品</button>
      <button class="tab-button {{ $tab == 'purchased' ? 'active' : '' }}" onclick="location.href='?tab=purchased'">購入した商品</button>
      <button class="tab-button {{ $tab == 'transaction' ? 'active' : '' }}" onclick="location.href='?tab=transaction'">取引中の商品
      @if($unreadTotal > 0)
        <span class="badge badge-danger badge-count" style="background:red; color:white;">{{ $unreadTotal }}</span>
      @endif
      </button>
    </div>

    <!-- 商品一覧 -->
@if($tab == 'listed')
  <div class="items">
    @foreach ($listedItems as $item)
      <div class="mb-4">
        <div class="card item-card">
          <a href="{{ route('item.show', ['item' => $item->id]) }}">
            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->name }}">
          </a>
          <div class="card-body">
            <h5 class="card-title">{{ $item->name }}</h5>
          </div>
        </div>
      </div>
    @endforeach
  </div>

@elseif($tab == 'purchased')
  <div class="items">
    @foreach ($purchasedItems as $purchase)
      @if($purchase->item)
      <div class="mb-4">
        <div class="card item-card">
          <a href="{{ route('item.show', ['item' => $purchase->item->id]) }}">
            <img src="{{ asset('storage/' . $purchase->item->image) }}" class="card-img-top" alt="{{ $purchase->item->name }}">
          </a>
          <div class="card-body">
            <h5 class="card-title">{{ $purchase->item->name }}</h5>
          </div>
        </div>
      </div>
      @endif
    @endforeach
  </div>

@elseif($tab == 'transaction')
  <div class="items">
    @foreach ($transactionItems as $item)
      <div class="mb-4">
        <div class="card item-card">
          <div class="image-wrapper">
            <a href="{{ route('transaction.show', ['item' => $item->id]) }}">
              <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="card-img-top">
            </a>
            @if($item->unread_count > 0)
              <span class="badge-unread">{{ $item->unread_count }}</span>
            @endif
          </div>
          <div class="card-body">
            <h5 class="card-title">{{ $item->name }}</h5>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

</div>
@endsection