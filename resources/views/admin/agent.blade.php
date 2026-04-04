@extends('layouts.app')
@section('title', 'Agent AI')
@section('module_name', 'ADMINISTRATOR')
@section('nav_menu') @include('admin._nav') @endsection

@section('styles')
<style>
.agent-layout { display:grid;grid-template-columns:260px 1fr;gap:0;height:calc(100vh - 60px);overflow:hidden; }

/* Sidebar historia */
.history-panel { background:#1a1a1a;color:#fff;display:flex;flex-direction:column;overflow:hidden; }
.history-header { padding:14px 16px;border-bottom:1px solid #333;display:flex;align-items:center;justify-content:space-between; }
.history-title { font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:#aaa; }
.btn-new-chat { background:#6c3483;color:#fff;border:none;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px; }
.btn-new-chat:hover { background:#5b2c6f; }
.history-list { flex:1;overflow-y:auto;padding:8px; }
.history-item { padding:10px 12px;border-radius:8px;cursor:pointer;margin-bottom:4px;display:flex;align-items:center;justify-content:space-between;gap:6px; }
.history-item:hover { background:#2d2d2d; }
.history-item.active { background:#6c3483; }
.hi-title { font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1; }
.hi-date { font-size:10px;color:#888;white-space:nowrap; }
.hi-del { background:none;border:none;color:#666;cursor:pointer;padding:2px 4px;border-radius:3px;font-size:11px;flex-shrink:0; }
.hi-del:hover { color:#e74c3c; }
.history-empty { padding:20px;text-align:center;color:#555;font-size:13px; }

/* Główna sekcja czatu */
.chat-section { display:flex;flex-direction:column;overflow:hidden;background:#f8f9fa; }
.chat-top { background:#6c3483;color:#fff;padding:12px 16px;display:flex;align-items:center;justify-content:space-between; }
.chat-top-title { font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:900;letter-spacing:.06em; }
.chat-messages { flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:14px; }
.msg { max-width:75%;border-radius:12px;padding:12px 16px;font-size:14px;line-height:1.6;word-break:break-word; }
.msg.user  { align-self:flex-end;background:#6c3483;color:#fff;border-bottom-right-radius:4px; }
.msg.agent { align-self:flex-start;background:#fff;color:#1a1a1a;border-bottom-left-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,.08); }
.msg.agent code { background:#f0f2f5;padding:1px 5px;border-radius:3px;font-size:12px; }
.msg.agent pre { background:#1a1a1a;color:#e8f5e9;padding:12px;border-radius:8px;overflow-x:auto;font-size:12px;margin:10px 0 0;line-height:1.5; }
.msg-time { font-size:10px;opacity:.5;margin-top:6px; }
.typing { display:flex;gap:5px;align-items:center;padding:12px 16px;background:#fff;border-radius:12px;align-self:flex-start;box-shadow:0 1px 3px rgba(0,0,0,.08); }
.typing span { width:8px;height:8px;background:#6c3483;border-radius:50%;animation:bounce .8s infinite alternate; }
.typing span:nth-child(2) { animation-delay:.2s; }
.typing span:nth-child(3) { animation-delay:.4s; }
@keyframes bounce { from{transform:translateY(0)} to{transform:translateY(-6px)} }

.chat-input-area { padding:14px 16px;background:#fff;border-top:1px solid #e2e5e9;display:flex;gap:8px;align-items:flex-end; }
.chat-input { flex:1;padding:10px 14px;border:1.5px solid #dde0e5;border-radius:10px;font-size:14px;outline:none;resize:none;font-family:inherit;max-height:120px; }
.chat-input:focus { border-color:#6c3483; }
.btn-send { padding:10px 20px;background:#6c3483;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;white-space:nowrap; }
.btn-send:hover { background:#5b2c6f; }
.btn-send:disabled { background:#ccc;cursor:not-allowed; }
</style>
@endsection

@section('content')
<div class="agent-layout">

    {{-- Panel historii --}}
    <div class="history-panel">
        <div class="history-header">
            <span class="history-title"><i class="fas fa-history"></i> Historia</span>
            <button class="btn-new-chat" onclick="newChat()">
                <i class="fas fa-plus"></i> Nowy
            </button>
        </div>
        <div class="history-list" id="historyList">
            @forelse($chats as $chat)
            <div class="history-item {{ $loop->first ? 'active' : '' }}"
                 id="hi-{{ $chat->id }}"
                 onclick="loadChat({{ $chat->id }}, {{ json_encode($chat->messages ?? []) }}, {{ json_encode($chat->title) }})">
                <div style="flex:1;overflow:hidden">
                    <div class="hi-title">{{ $chat->title }}</div>
                    <div class="hi-date">{{ $chat->updated_at->format('d.m H:i') }}</div>
                </div>
                <button class="hi-del" onclick="event.stopPropagation();deleteChat({{ $chat->id }})" title="Usuń">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            @empty
            <div class="history-empty">Brak historii czatów</div>
            @endforelse
        </div>
    </div>

    {{-- Sekcja czatu --}}
    <div class="chat-section">
        <div class="chat-top">
            <span class="chat-top-title" id="chatTitle"><i class="fas fa-robot"></i> Agent MrowiskoBIS</span>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="msg agent">
                Cześć! Jestem agentem AI dla systemu MrowiskoBIS. Mogę pomóc zrozumieć jak działa aplikacja lub zaproponować zmiany w kodzie.
                <div class="msg-time">Teraz</div>
            </div>
        </div>
        <div class="chat-input-area">
            <textarea class="chat-input" id="chatInput" rows="2"
                      placeholder="Wpisz pytanie... (Enter = wyślij, Shift+Enter = nowa linia)"
                      onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage()}"></textarea>
            <button class="btn-send" id="sendBtn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i> Wyślij
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const SYSTEM_PROMPT = `Jesteś asystentem dla systemu MrowiskoBIS – aplikacji Laravel 10 (PHP 8.3, MySQL, Bootstrap 5) dla firmy odpadowej.

Moduły: Biuro (planowanie, ważenia, raporty), Kierowca (zlecenia, ważenie), Plac (załadunki, dostawy, magazyn), Admin.
Statusy: Wysyłka: planned→loaded→weighed→closed. Odbiór: planned→weighed→delivered→closed.
Tabele: orders, clients, drivers, vehicles, vehicle_sets, waste_fractions, loading_items, warehouse_items, weighings, haulers, agent_chats.
Odpowiadaj po polsku, konkretnie. Przy zmianach podawaj nazwę pliku i co zmienić.`;

let messages = [];
let currentChatId = null;

@if($chats->isNotEmpty())
// Załaduj pierwszy czat
loadChat({{ $chats->first()->id }}, {!! json_encode($chats->first()->messages ?? []) !!}, {{ json_encode($chats->first()->title) }});
@endif

function loadChat(id, msgs, title) {
    currentChatId = id;
    messages = msgs || [];

    document.querySelectorAll('.history-item').forEach(el => el.classList.remove('active'));
    document.getElementById('hi-' + id)?.classList.add('active');
    document.getElementById('chatTitle').innerHTML = '<i class="fas fa-robot"></i> ' + title;

    const container = document.getElementById('chatMessages');
    container.innerHTML = '';

    if (messages.length === 0) {
        addMessage('agent', 'Cześć! Jak mogę pomóc?');
        return;
    }

    messages.forEach(m => addMessage(m.role === 'user' ? 'user' : 'agent', m.content, false));
    container.scrollTop = container.scrollHeight;
}

function newChat() {
    currentChatId = null;
    messages = [];
    document.querySelectorAll('.history-item').forEach(el => el.classList.remove('active'));
    document.getElementById('chatTitle').innerHTML = '<i class="fas fa-robot"></i> Nowy czat';
    const container = document.getElementById('chatMessages');
    container.innerHTML = '';
    addMessage('agent', 'Nowy czat. Jak mogę pomóc?');
    document.getElementById('chatInput').focus();
}

function addMessage(role, content, scroll = true) {
    const container = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = `msg ${role}`;
    const formatted = content
        .replace(/```(\w+)?\n([\s\S]*?)```/g, '<pre>$2</pre>')
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\n/g, '<br>');
    div.innerHTML = formatted + `<div class="msg-time">${new Date().toLocaleTimeString('pl')}</div>`;
    container.appendChild(div);
    if (scroll) container.scrollTop = container.scrollHeight;
}

function addTyping() {
    const container = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.id = 'typing';
    div.className = 'typing';
    div.innerHTML = '<span></span><span></span><span></span>';
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

async function sendMessage() {
    const input = document.getElementById('chatInput');
    const text = input.value.trim();
    if (!text) return;

    input.value = '';
    addMessage('user', text);
    messages.push({ role: 'user', content: text });

    document.getElementById('sendBtn').disabled = true;
    addTyping();

    try {
        const res = await fetch('/admin/agent/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ messages, system: SYSTEM_PROMPT }),
        });
        const data = await res.json();
        document.getElementById('typing')?.remove();

        if (data.success) {
            messages.push({ role: 'assistant', content: data.content });
            addMessage('agent', data.content);
            await saveChat();
        } else {
            addMessage('agent', '⚠️ Błąd: ' + (data.error ?? 'Nieznany'));
        }
    } catch (e) {
        document.getElementById('typing')?.remove();
        addMessage('agent', '⚠️ Błąd połączenia: ' + e.message);
    }

    document.getElementById('sendBtn').disabled = false;
    input.focus();
}

async function saveChat() {
    // Generuj tytuł z pierwszej wiadomości użytkownika
    const firstUser = messages.find(m => m.role === 'user');
    const title = firstUser ? firstUser.content.substring(0, 40) + (firstUser.content.length > 40 ? '...' : '') : 'Czat';

    const res = await fetch('/admin/agent/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ chat_id: currentChatId, title, messages }),
    });
    const data = await res.json();
    if (data.success) {
        currentChatId = data.chat_id;
        updateHistorySidebar(data.chat_id, data.title);
    }
}

function updateHistorySidebar(id, title) {
    const list = document.getElementById('historyList');
    let item = document.getElementById('hi-' + id);

    if (!item) {
        item = document.createElement('div');
        item.id = 'hi-' + id;
        item.className = 'history-item active';
        item.setAttribute('onclick', `loadChat(${id}, [], ${JSON.stringify(title)})`);
        item.innerHTML = `
            <div style="flex:1;overflow:hidden">
                <div class="hi-title">${title}</div>
                <div class="hi-date">${new Date().toLocaleTimeString('pl', {hour:'2-digit',minute:'2-digit'})}</div>
            </div>
            <button class="hi-del" onclick="event.stopPropagation();deleteChat(${id})" title="Usuń">
                <i class="fas fa-trash"></i>
            </button>`;
        // Usuń "brak historii" jeśli jest
        list.querySelector('.history-empty')?.remove();
        list.prepend(item);
    }

    document.querySelectorAll('.history-item').forEach(el => el.classList.remove('active'));
    item.classList.add('active');
    item.querySelector('.hi-title').textContent = title;
    document.getElementById('chatTitle').innerHTML = '<i class="fas fa-robot"></i> ' + title;
}

async function deleteChat(id) {
    const res = await fetch(`/admin/agent/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF },
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('hi-' + id)?.remove();
        if (currentChatId === id) newChat();
    }
}
</script>
@endsection
