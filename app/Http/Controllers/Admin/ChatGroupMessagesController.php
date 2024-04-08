<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatGroupMessageRecalled;
use App\Events\ChatGroupMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatGroupMessageResource;
use App\ModelFilters\Admin\ChatGroupMessageFilter;
use App\Models\ChatGroup;
use App\Models\ChatGroupMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatGroupMessagesController extends Controller
{
    public function index(Request $request)
    {
        $messages = ChatGroupMessage::filter($request->all(), ChatGroupMessageFilter::class)->with(['group', 'user'])->latest()->paginate($request->page_size ?? 15);

        return ChatGroupMessageResource::collection($messages);
    }

    public function send(Request $request)
    {
        $messages = DB::transaction(function () use ($request) {
            $groups = ChatGroup::query()->whereIn('id', $request->group_ids)->get();
            $user = User::query()->where('username', User::USERNAME_ADMINISTRATOR)->first();
            $messages = collect();
            foreach ($groups as $group) {
                $message = new ChatGroupMessage([
                    'body' => [
                        [
                            'MsgType' => 'TIMTextElem',
                            'MsgContent' => [
                                "Text" => $request->input('text')
                            ]
                        ]
                    ]
                ]);
                $message->group()->associate($group);
                $message->user()->associate($user);
                $message->sent_at = now();
                $message->save();
                $messages->push($message);

                event(new ChatGroupMessageSent($message));
            }

            return $messages;
        });

        return ChatGroupMessageResource::collection($messages);
    }

    public function recall(Request $request)
    {
        $messages = ChatGroupMessage::query()->whereIn('id', $request->message_ids)->where('status', ChatGroupMessage::STATUS_NORMAL)->get();

        foreach ($messages as $message) {
            $message->status = ChatGroupMessage::STATUS_RECALL;
            $message->save();

            event(new ChatGroupMessageRecalled($message));
        }

        return ChatGroupMessageResource::collection($messages);
    }
}
