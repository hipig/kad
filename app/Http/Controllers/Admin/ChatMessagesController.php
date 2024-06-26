<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageSent;
use App\Events\ChatMessageWithdrew;
use App\Exports\ChatMessageExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatGroupMessageResource;
use App\Http\Resources\ChatMessageResource;
use App\ModelFilters\Admin\ChatMessageFilter;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatMessagesController extends Controller
{
    public function index(Request $request)
    {
        $messages = ChatMessage::filter($request->all(), ChatMessageFilter::class)->with(['user', 'toUser'])->latest()->paginate($request->page_size ?? 15);

        return ChatMessageResource::collection($messages);
    }

    public function store(Request $request)
    {
        $messages = DB::transaction(function () use ($request) {
            $user = User::query()->where('username', User::USERNAME_ADMINISTRATOR)->first();
            if ($userId = $request->user_id) {
                $user = User::query()->where('id', $userId)->first();
            }
            $toUsers = User::query()->whereIn('id', $request->to_user_ids)->get();

            $messages = collect();
            foreach ($toUsers as $toUser) {
                $message = new ChatMessage([
                    'body' => [
                        [
                            'MsgType' => 'TIMTextElem',
                            'MsgContent' => [
                                "Text" => $request->input('text')
                            ]
                        ]
                    ]
                ]);

                $message->user()->associate($user);
                $message->toUser()->associate($toUser);
                $message->sent_at = now();
                $message->save();
                $messages->push($message);

                event(new ChatMessageSent($message));
            }

            return $messages;
        });

        return ChatMessageResource::collection($messages);
    }

    public function withdraw(Request $request)
    {
        $messages = ChatMessage::query()->whereIn('id', $request->message_ids)->where('status', ChatMessage::STATUS_NORMAL)->get();

        foreach ($messages as $message) {
            $message->status = ChatMessage::STATUS_WITHDRAW;
            $message->save();

            event(new ChatMessageWithdrew($message));
        }

        return ChatGroupMessageResource::collection($messages);
    }

    public function export(Request $request)
    {
        return (new ChatMessageExport($request->all()))->download('单聊记录列表.xlsx');
    }
}
