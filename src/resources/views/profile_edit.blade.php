@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 500px;">
    <h2 class="text-center mb-4">プロフィール設定</h2>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <!-- プロフィール画像 -->
        <div class="form-group mb-3 text-center">
        @if (auth()->user()->image)
          <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="プロフィール画像" class="img-thumbnail mb-2" style="width: 150px; height: 150px; object-fit: cover;">
          @else
          <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルト画像" class="img-thumbnail mb-2" style="width: 150px; height: 150px; object-fit: cover;">
        @endif

          <div>
            <label for="image" class="form-label">画像を選択する</label>
              <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
              @error('image')
              <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
          </div>
        </div>

        <!-- ユーザー名 -->
        <div class="form-group mb-3">
          <label for="name">ユーザー名</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                name="name" value="{{ old('name',$user->name) }}" >
                @error('name')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
        </div>

        <!-- 郵便番号 -->
        <div class="form-group mb-3">
          <label for="postal_code">郵便番号</label>
            <input id="postal_code" type="text" class="form-control @error('postal_code') is-invalid @enderror"
                name="postal_code" value="{{ old('postal_code',$user->postal_code) }}" >
              @error('postal_code')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
        </div>

        <!-- 住所 -->
        <div class="form-group mb-3">
            <label for="address">住所</label>
            <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                  name="address" value="{{ old('address',$user->address) }}" >
            @error('address')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- 建物名 -->
        <div class="form-group mb-4">
            <label for="building">建物名</label>
            <input id="building" type="text" class="form-control"
                  name="building" value="{{ old('building',$user->building) }}" >
        </div>

        <!-- 登録ボタン -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-danger">更新する</button>
        </div>
    </form>
</div>
@endsection
