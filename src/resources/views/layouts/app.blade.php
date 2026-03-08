<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COACHTECH</title>
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  @yield('css')
</head>

<body>
  <header class="bg-dark text-white py-2">
    <div class="header-bar">
      <!-- 左側：ロゴ -->
      <div class="header-logo">
        <a href="{{ route('top') }}" class="header-logo-link">
          <img src="{{ asset('images/header-logo.png') }}" class="header-logo-image">
        </a>
      </div>

      <!-- 中央：検索バー -->
      @if (!in_array(Route::currentRouteName() ?? '', ['login', 'register']))
      <div class="header-search">
        <form action="{{ route('top') }}" method="GET">
          <input type="text" name="keyword" class="header-search-input" placeholder="何をお探しですか？" value="{{ request('keyword') }}">
        </form>
      </div>

        <!-- 右側：ナビゲーション -->
      <div class="header-nav text-end">
      @auth
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-light btn-sm me-2">ログアウト</button>
        </form>
        <a href="{{ route('profile') }}" class="btn btn-outline-light btn-sm me-2">マイページ</a>
        <a href="{{ route('item_shipping') }}" class="btn btn-outline-light btn-sm">出品</a>
      @endauth

      @guest
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">ログイン</a>
        <a href="{{ route('profile') }}" class="btn btn-outline-light btn-sm me-2">マイページ</a>
        <a href="{{ route('item_shipping') }}" class="btn btn-outline-light btn-sm">出品</a>
      @endguest
      </div>
      @endif
    </div>
  </header>

  <main>
  @yield('content')
  </main>

<script src="{{ mix('js/app.js') }}"></script>
@yield('js')
@yield('scripts')

</body>
</html>
