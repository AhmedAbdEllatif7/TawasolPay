@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- رأس المحادثة -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary rounded-circle p-2">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                        <h5 class="mb-0 fw-bold">{{ $otherUser->name }}</h5>
                    </div>
                </div>
            </div>

            <!-- منطقة المحادثة -->
            <div class="chat-container bg-white rounded-3 shadow-sm p-3 mb-3" style="min-height: 60vh; max-height: 60vh; overflow-y: auto;">
                @if($messages->count() > 0)
                    @foreach($messages as $message)
                        <div class="message-wrapper {{ $message->sender_id == Auth::id() ? 'text-start' : 'text-end' }}" data-message-id="{{ $message->id }}">
                            <div class="message-bubble {{ $message->sender_id == Auth::id() ? 'bg-primary text-white' : 'bg-light' }} rounded-3 p-3 mb-2" style="max-width: 80%; display: inline-block;">
                                <div class="message-content" style="word-wrap: break-word; white-space: pre-wrap;">{{ $message->message }}</div>
                                <div class="message-time small mt-1 {{ $message->sender_id == Auth::id() ? 'text-white-50' : 'text-muted' }}" 
                                    data-timestamp="{{ $message->created_at->timestamp }}">
                                    {{ $message->created_at->diffForHumans() }}
                                    @if($message->sender_id == Auth::id())
                                        <i class="fas fa-check{{ $message->is_read ? '-double text-info' : '' }} ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-chat d-flex flex-column justify-content-center align-items-center h-100 text-center py-5">
                        <div class="empty-icon bg-light rounded-circle p-4 mb-3">
                            <i class="fas fa-comments fa-2x text-muted"></i>
                        </div>
                        <h5 class="mb-2">لا توجد رسائل بعد</h5>
                        <p class="text-muted mb-4">إبدأ المحادثة مع {{ $otherUser->name }}</p>
                        <div class="badge bg-info text-white px-3 py-2 rounded-pill">
                            <i class="fas fa-info-circle me-2"></i>أنت الآن تتحدث مع {{ $otherUser->name }}
                        </div>
                    </div>
                @endif
            </div>

            @livewire('chat.form-chat', ['conversationId' => $conversation->id])

        </div>
    </div>
</div>

<style>
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
    }
    
    .chat-container {
        scrollbar-width: thin;
        scrollbar-color: #ddd #f8f9fa;
    }
    
    .chat-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .chat-container::-webkit-scrollbar-track {
        background: #f8f9fa;
    }
    
    .chat-container::-webkit-scrollbar-thumb {
        background-color: #ddd;
        border-radius: 20px;
    }
    
    .message-bubble {
        position: relative;
        word-break: break-word;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* تحسينات للرسائل الطويلة */
    .message-content {
        white-space: pre-wrap;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    @media (max-width: 768px) {
        .message-bubble {
            max-width: 90% !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // جعل المحادثة تظهر من الأسفل
        const chatContainer = document.querySelector('.chat-container');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // إعداد Pusher
    const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
            forceTLS: true
        });

    // الاشتراك في القنوات
    const messageChannel = pusher.subscribe('show-message-channel');
    const readChannel = pusher.subscribe('message-read-channel');

    // معالجة الرسائل الجديدة
    messageChannel.bind('new-message', function(data) {
        if (!chatContainer) return;

        const isSender = data.message.sender_id === {{ Auth::id() }};
        const isCurrentConversation = data.message.conversation_id === {{ $conversation->id }};
        
        // عرض الرسالة (دائماً بعلامة صح واحدة أولاً)
        const messageHTML = `
            <div class="message-wrapper ${isSender ? 'text-start' : 'text-end'}" data-message-id="${data.message.id}">
                <div class="message-bubble ${isSender ? 'bg-primary text-white' : 'bg-light'} rounded-3 p-3 mb-2" style="max-width: 80%; display: inline-block;">
                    <div class="message-content" style="word-wrap: break-word; white-space: pre-wrap;">${data.message.message}</div>
                    <div class="message-time small mt-1 ${isSender ? 'text-white-50' : 'text-muted'}" data-timestamp="${Math.floor(Date.now() / 1000)}">
                        الآن
                        ${isSender ? '<i class="fas fa-check ms-1"></i>' : ''}
                    </div>
                </div>
            </div>
        `;

        chatContainer.insertAdjacentHTML('beforeend', messageHTML);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // إذا كانت رسالة واردة في المحادثة الحالية، نرسل طلب تحديث القراءة
        if (!isSender && isCurrentConversation) {
            Livewire.dispatch('messagesRead', { messageId: data.message.id });
        }
    });

    // معالجة أحداث تحديث القراءة
    readChannel.bind('message-read', function(data) {
        // إذا كان هناك معرف رسالة محدد، نحدثها فقط
        if (data.messageId) {
            const messageElement = document.querySelector(`[data-message-id="${data.messageId}"] .fa-check`);
            if (messageElement) {
                messageElement.classList.replace('fa-check', 'fa-check-double');
                messageElement.classList.add('text-info');
            }
        } 
        // إذا لم يكن هناك معرف رسالة، نحدث كل الرسائل في المحادثة
        else if (data.conversationId === {{ $conversation->id }}) {
            document.querySelectorAll(`[data-message-id] .fa-check`).forEach(icon => {
                icon.classList.replace('fa-check', 'fa-check-double');
                icon.classList.add('text-info');
            });
        }
    });

    function updateMessageTimes() {
        const timeElements = document.querySelectorAll('.message-time[data-timestamp]');
        timeElements.forEach(el => {
            // بنسيب علامات الصح كما هي ونعدل الوقت فقط
            const timeTextElement = el.childNodes[0]; // أول عنصر داخل الـ div هو النص
            const checkIcon = el.querySelector('i'); // علامة الصح لو موجودة
            
            const sentTime = parseInt(el.getAttribute('data-timestamp')) * 1000;
            const diffSeconds = Math.floor((Date.now() - sentTime) / 1000);

            let timeText = "الآن";
            if (diffSeconds > 0) {
                if (diffSeconds < 60) {
                    timeText = `منذ ${diffSeconds} ثانية`;
                } else if (diffSeconds < 3600) {
                    const minutes = Math.floor(diffSeconds / 60);
                    timeText = `منذ ${minutes} دقيقة`;
                } else if (diffSeconds < 86400) {
                    const hours = Math.floor(diffSeconds / 3600);
                    timeText = `منذ ${hours} ساعة`;
                } else {
                    const day = Math.floor(diffSeconds / 86400);
                    timeText = `منذ ${day} يوم`;
                }
            }
            
            // نعدل النص بس من غير ما نمس علامات الصح
            if (timeTextElement) {
                timeTextElement.textContent = timeText;
            }
        });
    }
    setInterval(updateMessageTimes, 5000);
});
</script>
@endsection