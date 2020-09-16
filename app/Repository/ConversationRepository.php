<?php

namespace App\Repository;

use App\User;
use App\Message;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ConversationRepository{

    /**
     * @var User
     */
    private $user;
    /**
     * @var Message
     */
    private $message;

    public function __construct(User $user, Message $message){
        $this->user = $user;
        $this->message = $message;
    }

    public function getConversation(int $userId){
        return $this->user->newQuery()->select('name', 'id')->where('id', '!=', $userId)->get();
    }

    /**
     * Récupére le nombre de message non lus pour chaque conversation
     * @param int $userId
     */
    public function unreadCoun(int $userId){
        return $this->message->newQuery()
            ->where('to_id', $userId)
            ->groupBy('from_id')
            ->selectRaw('from_id, COUNT(id) AS count')
            ->whereRaw('read_at IS NULL')
            ->get()
            ->pluck('count', 'from_id');
    }

    public function createMessage(string $content, int $from, int $to){
        return $this->message->newQuery()->create([
            'content' => $content,
            'from_id' => $from,
            'to_id' => $to,
            'created_at' => Carbon::now()
        ]);
    }

    public function getMessageFor(int $from, int $to) : Builder
    {
        return $this->message->newQuery()
            ->whereRaw("((from_id = $from AND to_id = $to) OR (from_id = $to AND to_id = $from))")
            ->orderBy('created_at', 'DESC')
            ->with([
                'from' => function($query){
                return $query->select('name', 'id');
                }
            ]);
    }

    /**
     * marquer toute les messages de l'utilisateur comme lu
     * @param int $from
     * @param int $to_id
     */
    public function readAllFrom(int $from, int $to){
        $this->message->where('from_id', $from)->where('to_id', $to)->update(['read_at' => Carbon::now()]);
    }
}
