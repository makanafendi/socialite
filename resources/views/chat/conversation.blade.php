@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Chat with {{ $user->name }}</h1>
    <div class="bg-white p-4 shadow-md rounded-md">
        <div id="chat-box" class="h-64 overflow-y-auto border p-3"></div>

        <form id="chat-form" class="mt-4">
            <input type="hidden" id="receiver_id" value="{{ $user->id }}">
            <input type="text" id="message" class="border p-2 w-full" placeholder="Type a message...">
            <button type="submit" class="bg-blue-500 text-white px-3 py-1 mt-2">Send</button>
        </form>
    </div>
</div>

<script>
    const authUserId = {{ Auth::id() }};
    const receiverId = document.getElementById('receiver_id').value;
    const chatBox = document.getElementById('chat-box');

    function loadMessages() {
        fetch(`/chat/messages/${receiverId}`)
            .then(response => response.json())
            .then(messages => {
                chatBox.innerHTML = messages.map(msg =>
                    `<div><strong>${msg.sender_id == authUserId ? 'Me' : '{{ $user->name }}'}</strong>: ${msg.message}</div>`
                ).join('');
                chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll to the bottom
            });
    }

    document.getElementById('chat-form').addEventListener('submit', function (e) {
        e.preventDefault();
        let message = document.getElementById('message').value;

        fetch('/chat/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ message, receiver_id: receiverId })
        }).then(response => response.json())
          .then(data => {
              document.getElementById('message').value = '';
              loadMessages(); // Fetch messages immediately after sending
          });
    });

    // Fetch messages every 3 seconds (polling)
    setInterval(loadMessages, 5000);

    loadMessages(); // Load messages when page loads
</script>
@endsection
