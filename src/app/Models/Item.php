<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\ItemLike;
use App\Models\ItemComment;
use App\Models\Purchase;


class Item extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'description',
      'price',
      'brand',
      'condition',
      'image',
      'user_id',
      ];

    public function user()
      {
      return $this->belongsTo(User::class);
      }

      public function categories()
      {
      return $this->belongsToMany(Category::class, 'item_categories');
      }

      public function likes()
      {
      return $this->hasMany(ItemLike::class);
      }

      public function comments()
      {
      return $this->hasMany(ItemComment::class);
      }

      public function likedByUsers()
      {
      return $this->belongsToMany(User::class, 'item_likes')->withTimestamps();
      }

      public function isSold(): bool
      {
      return $this->purchase !== null;
      }

      public function purchase()
      {
      return $this->hasOne(Purchase::class);
      }

      public function messages()
      {
      return $this->hasMany(Message::class, 'item_id', 'id');
      }

      public function latestMessage()
      {
      return $this->hasOne(Message::class, 'item_id', 'id')->latestOfMany();
      }

}
