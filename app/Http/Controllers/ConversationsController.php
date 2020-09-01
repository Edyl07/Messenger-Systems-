<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
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
        $this->auth = $auth;
        $this->conversationRepository = $conversationRepository;
    }

    public function index(){
        return view('conversations.index', [
            'users' => $this->conversationRepository->getConversation($this->auth->user()->id)
        ]);
    }

    public function show(User $user){
        return view('conversations.show', [
            'users' => $this->conversationRepository->getConversation($this->auth->user()->id),
            'user' => $user,
            'messages' => $this->conversationRepository->getMessageFor($this->auth->user()->id, $user->id)->paginate(50)
        ]);
    }

    public function store(User $user, StoreMessageRequest $request){
         $this->conversationRepository->createMessage(
            $request->get('content'),
            $this->auth->user()->id,
            $user->id
        );
         return redirect(route('conversations.show', ['id' => $user->id]));
    }
}
