<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Notifications\MessageReceived;
use App\Repository\ConversationRepository;
use App\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationsController extends Controller
{

    /**
     * @var ConversationRepository
     */
    private $conversationRepository;
    /**
     * @var AuthManager
     */
    private $auth;

    public function __construct(ConversationRepository $conversationRepository, AuthManager $auth)
    {
        $this->middleware('auth');
        $this->auth = $auth;
        $this->conversationRepository = $conversationRepository;
    }

    public function index(){
        return view('conversations.index', [
            'users' => $this->conversationRepository->getConversation($this->auth->user()->id),
            'unread' => $this->conversationRepository->unreadCoun($this->auth->user()->id)
        ]);
    }

    public function show(User $user){
        $me = $this->auth->user();
        $message = $this->conversationRepository->getMessageFor($me->id, $user->id)->paginate(50);
        $unread = $this->conversationRepository->unreadCoun($me->id);
        if (isset($unread[$user->id])){
            $this->conversationRepository->readAllFrom($user->id, $me->id);
            $unread[$user->id] = 0;
        }
        return view('conversations.show', [
            'users' => $this->conversationRepository->getConversation($me->id),
            'user' => $user,
            'messages' => $message,
            'unread' => $this->conversationRepository->unreadCoun($me->id)
        ]);
    }

    public function store(User $user, StoreMessageRequest $request){
         $message = $this->conversationRepository->createMessage(
            $request->get('content'),
            $this->auth->user()->id,
            $user->id
        );
         //$user->notify(new MessageReceived($message));
         return redirect()->route('conversations.show', ['id' => $user]);
    }
}
