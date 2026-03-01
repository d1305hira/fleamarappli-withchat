<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;
use App\Models\Rating;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MessageRequest;
use App\Mail\TransactionCompletedMail;
use Illuminate\Support\Facades\Mail;


class TransactionController extends Controller
{
    public function show(Item $item)
{
    $user = auth()->user();

    // 自分宛ての未読メッセージを既読にする
    Message::where('item_id', $item->id)
        ->where('to_user_id', $user->id)
        ->where('is_read', 0)
        ->update(['is_read' => 1]);

    // ここから下は今のままでOK
    $item->load('user', 'purchase.user');

    $messages = Message::where('item_id', $item->id)
        ->with('user')
        ->orderBy('created_at')
        ->get();

      // ★ 最新メッセージの created_at を item_id ごとにまとめるサブクエリ
      $latestMessages = Message::select(
          'item_id',
          DB::raw('MAX(created_at) as latest_created_at')
        )
        ->groupBy('item_id');

      // ★ その他の取引を「最新メッセージ順」に並び替える
      $otherItems = Item::where('id', '!=', $item->id)
    ->where(function($q) use ($user) {

        // ▼ 購入者側の取引中条件
        $q->where(function($q) use ($user) {
            $q->whereHas('purchase', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where(function($q) {
                $q->where('buyer_completed', false) // 購入者がまだ完了していない
                  ->orWhere(function($q) {
                      $q->where('buyer_completed', true)
                        ->where('seller_completed', true)
                        ->where('is_sold', '!=', 4); // 購入者がまだ評価していない
                  });
            });
        })

        // ▼ 出品者側の取引中条件
->orWhere(function($q) use ($user) {
    $q->where('user_id', $user->id)
      ->whereHas('purchase')
      ->where(function($q) {
          $q->where('seller_completed', false)
            ->orWhere(function($q) {
                $q->where('buyer_completed', true)
                  ->where('seller_completed', true)
                  ->where('is_sold', '!=', 4);
            });
      });
});


    })
    ->leftJoinSub($latestMessages, 'latest_messages', function($join) {
        $join->on('items.id', '=', 'latest_messages.item_id');
    })
    ->with(['latestMessage'])
    ->orderByDesc('latest_messages.latest_created_at')
    ->select('items.*')
    ->get();

        $isCompleted =
          auth()->id() === $item->purchase->user_id &&
          !$item->buyer_completed;

        
        return view('transaction', compact('item', 'messages', 'otherItems', 'isCompleted'));

}


    public function store(MessageRequest $request, Item $item)
{
    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('message_images', 'public');
    }

    $senderId = auth()->id();
    $sellerId = $item->user_id;
    $buyerId = $item->purchase->user_id;

    // ★ 受信者を決定（1対1チャット）
    $receiverId = ($senderId == $sellerId) ? $buyerId : $sellerId;

    $message = Message::create([
        'item_id' => $item->id,
        'user_id' => $senderId,
        'to_user_id' => $receiverId,
        'message' => $request->message,
        'image' => $imagePath,
        'is_read' => false,
    ]);

    broadcast(new MessageSent($message))->toOthers();

    return back();
}


    public function complete(Request $request, Item $item)
{

    $item->load('purchase.user');
    $rating = $request->input('rating');

    $buyerId = $item->purchase->user_id;
    $sellerId = $item->user_id;
    $currentUserId = auth()->id();

    // 評価対象
    $ratedUserId = $currentUserId === $sellerId ? $buyerId : $sellerId;

    // 重複評価防止
    $alreadyRated = Rating::where('item_id', $item->id)
        ->where('user_id', $currentUserId)
        ->exists();

    if (!$alreadyRated) {
        Rating::create([
            'item_id' => $item->id,
            'user_id' => $currentUserId,
            'rating' => $rating,
            'rated_user_id' => $ratedUserId,
        ]);
    }

    // 完了フラグ更新
if ($currentUserId === $buyerId) {
    $item->buyer_completed = true;
}

if ($currentUserId === $sellerId) {
    $item->seller_completed = true;
}

$item->save();

$item->refresh();

// 購入者が完了したときに出品者へメール送信
if ($currentUserId === $buyerId) {
    Mail::to($item->user->email)
        ->send(new TransactionCompletedMail($item, $item->purchase->user));
}

// 両者評価済みか
$sellerRated = Rating::where('item_id', $item->id)
    ->where('user_id', $sellerId)
    ->exists();

$buyerRated = Rating::where('item_id', $item->id)
    ->where('user_id', $buyerId)
    ->exists();

if ($buyerRated && $sellerRated) {
    $item->is_sold = 4;
    $item->save();
}


    return response()->json(['success' => true]);
}



    public function updateMessage(Request $request, Message $message)
    {
        // 自分のメッセージ以外は編集不可
        if (!$message->isOwnedBy(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:400',
            'image' => 'nullable|mimes:jpeg,png',
        ]);

        $message->message = $request->message;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('message_images', 'public');
            $message->image = $imagePath;
        }

        $message->save();

        return back();
    }

    public function destroyMessage(Message $message)
    {
        if (!$message->isOwnedBy(auth()->user())) {
            abort(403);
        }

        $message->delete();

        return back();
    }
}
