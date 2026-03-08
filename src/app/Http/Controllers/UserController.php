<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function show(Request $request)
    {
      $user = auth()->user();
      $tab = $request->tab ?? 'listed';

      $listedItems = Item::where('user_id', $user->id)->get();

      $purchasedItems = Purchase::where('user_id', $user->id)
        ->whereHas('item', function($q) {
            $q->where('is_sold', 4);
            })
        ->with('item')
        ->get();

      // ★ 最新メッセージの created_at を item_id ごとにまとめるサブクエリ
      $latestMessages = Message::select(
            'item_id',
             DB::raw('MAX(created_at) as latest_created_at')
            )
        ->groupBy('item_id');

      $unreadTotal = Message::where('to_user_id', $user->id)
        ->where('is_read', false)
        ->count();


      // ★ 取引中アイテムを「最新メッセージ順」に並び替える完全版
      // ▼ 購入者としての取引中アイテムID
      $buyerItemIds = Item::whereHas('purchase', function($q) use ($user) {
        $q->where('user_id', $user->id);
        })
        ->where('is_sold', '!=', 4)
        ->where('buyer_completed', false)
        ->pluck('id');

      // ▼ 出品者としての取引中アイテムID
      $sellerItemIds = Item::where('user_id', $user->id)
        ->whereHas('purchase')
        ->where('is_sold', '!=', 4)
        ->where('seller_completed', false)
        ->pluck('id');

      // ▼ 両方のIDを結合
      $allIds = $buyerItemIds->merge($sellerItemIds)->unique();

      // ▼ 最新メッセージ順に並び替えて取得
      $transactionItems = Item::whereIn('id', $allIds)
        ->with(['user'])
        ->withMax('messages', 'created_at') // ★ 最新メッセージの created_at を取得
        ->withCount([
        'messages as unread_count' => function($q) use ($user) {
            $q->where('to_user_id', $user->id)
              ->where('is_read', false);
            }
        ])
        ->orderByDesc('messages_max_created_at') // ★ 最新メッセージ順に並び替え
        ->get();

      $transactionCount = $allIds->count();
      $averageRating = $user->receivedRatings()->avg('rating');
      $ratingCount = $user->receivedRatings()->count();

      return view('profile', compact(
        'user',
        'listedItems',
        'purchasedItems',
        'transactionItems',
        'tab',
        'transactionCount',
        'averageRating',
        'ratingCount',
        'unreadTotal'
        ));
    }

    public function edit()
    {
      $user = auth()->user();
      return view('profile_edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
      $user = auth()->user();

      if ($request->hasFile('image')) {
        $path = $request->file('image')->store('profile_images', 'public');
        $user->image = $path;
        }

      $user->name = $request->name;
      $user->postal_code = $request->postal_code;
      $user->address = $request->address;
      $user->building = $request->building;

      $user->save();

      return redirect('/')->with('success', 'プロフィールを更新しました');
    }
}