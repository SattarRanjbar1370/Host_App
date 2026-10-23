<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'parent_id',
        'title',
        'body',
        'is_urgent',
        'is_read',
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'is_read' => 'boolean',
    ];

    // فرستنده پیام
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // گیرنده پیام (در صورت خصوصی بودن)
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    // پیام والد جهت پاسخ
    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }
}