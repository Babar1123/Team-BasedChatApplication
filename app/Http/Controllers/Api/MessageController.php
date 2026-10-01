<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Channel;
use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Request $request, Channel $channel)
    {
        if (! Gate::allows('view', $channel)) {
            return response()->error('Forbidden', 403);
        }

        $messages = Message::with(['user', 'parent', 'reactions.user'])
            ->where('channel_id', $channel->id)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->success('Messages fetched successfully', MessageResource::collection($messages), [
            'current_page' => $messages->currentPage(),
            'last_page' => $messages->lastPage(),
            'per_page' => $messages->perPage(),
            'total' => $messages->total(),
        ]);
    }

    public function store(SendMessageRequest $request, Channel $channel)
    {
        if (! Gate::allows('view', $channel)) {
            return response()->error('Forbidden', 403);
        }

        $message = Message::create([
            'channel_id' => $channel->id,
            'user_id' => $request->user()->id,
            'message' => $request->validated()['message'],
        ]);

        return response()->success('Message sent', new MessageResource($message->load('user')));
    }

    public function show(Request $request, Message $message)
    {
        if (! Gate::allows('view', $message)) {
            return response()->error('Forbidden', 403);
        }

        return response()->success('Message fetched', new MessageResource($message->load('user', 'parent.user', 'reactions.user')));
    }

    public function update(SendMessageRequest $request, Message $message)
    {
        if (! Gate::allows('update', $message)) {
            return response()->error('Forbidden', 403);
        }

        $message->update($request->validated());

        return response()->success('Message updated', new MessageResource($message->load('user')));
    }

    public function destroy(Request $request, Message $message)
    {
        if (! Gate::allows('delete', $message)) {
            return response()->error('Forbidden', 403);
        }

        $message->delete();

        return response()->success('Message deleted');
    }

    public function attach(Request $request, Message $message)
    {
        if (! Gate::allows('update', $message)) {
            return response()->error('Forbidden', 403);
        }

        if (! $request->hasFile('file')) {
            return response()->error('File is required', 422);
        }

        $file = $request->file('file');
        $path = $file->store('message_attachments', 'public');

        $attachment = $message->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->success('File uploaded', $attachment);
    }

    public function reply(Request $request, Message $message)
    {
        if (! Gate::allows('view', $message)) {
            return response()->error('Forbidden', 403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $reply = Message::create([
            'channel_id' => $message->channel_id,
            'user_id' => $request->user()->id,
            'parent_id' => $message->id,
            'message' => $data['message'],
        ]);

        return response()->success('Reply sent', new MessageResource($reply->load('user', 'parent.user')));
    }

    public function replies(Request $request, Message $message)
    {
        if (! Gate::allows('view', $message)) {
            return response()->error('Forbidden', 403);
        }

        $replies = $message->replies()->with('user')->paginate(20);

        return response()->success('Replies fetched', MessageResource::collection($replies));
    }

    public function reactionsIndex(Request $request, Message $message)
    {
        if (! Gate::allows('view', $message)) {
            return response()->error('Forbidden', 403);
        }

        $reactions = $message->reactions()->with('user')->get();

        return response()->success('Reactions fetched', $reactions);
    }

    public function storeReaction(Request $request, Message $message)
    {
        if (! Gate::allows('view', $message)) {
            return response()->error('Forbidden', 403);
        }

        $data = $request->validate([
            'reaction' => ['required', 'in:like,love,laugh,celebrate,rocket'],
        ]);

        $reaction = MessageReaction::firstOrCreate([
            'message_id' => $message->id,
            'user_id' => $request->user()->id,
            'reaction' => $data['reaction'],
        ]);

        return response()->success('Reaction saved', $reaction->load('user'));
    }

    public function destroyReaction(Request $request, Message $message, string $reaction)
    {
        if (! Gate::allows('view', $message)) {
            return response()->error('Forbidden', 403);
        }

        $message->reactions()->where('user_id', $request->user()->id)->where('reaction', $reaction)->delete();

        return response()->success('Reaction removed');
    }
}
