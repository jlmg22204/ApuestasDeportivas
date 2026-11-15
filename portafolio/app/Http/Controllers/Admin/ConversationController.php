<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $onlyLeads = $request->boolean('leads');

        $conversations = ChatConversation::query()
            ->withCount('messages')
            ->with('lead')
            ->when($onlyLeads, fn ($q) => $q->has('lead'))
            ->latest('last_message_at')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'conversations' => ChatConversation::count(),
            'leads' => ChatLead::count(),
            'new_leads' => ChatLead::where('status', 'nuevo')->count(),
            'tokens' => ChatConversation::sum('input_tokens') + ChatConversation::sum('output_tokens'),
        ];

        return view('admin.conversations.index', compact('conversations', 'stats', 'onlyLeads'));
    }

    public function show(ChatConversation $conversation): View
    {
        $conversation->load(['messages', 'lead']);

        return view('admin.conversations.show', [
            'conversation' => $conversation,
            'statuses' => ChatLead::STATUSES,
        ]);
    }

    public function updateLead(Request $request, ChatLead $lead): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(ChatLead::STATUSES)]]);
        $lead->update($data);

        return back()->with('status', 'Estado del lead actualizado.');
    }
}
