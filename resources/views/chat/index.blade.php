@extends('layouts.app')

@section('content')
<style>
    .avatar {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #00b894, #55efc4);
    }
    .bg-hover-light:hover {
        background-color: #f8f9fa;
    }
    .empty-state {
        background-color: #f8f9fa;
    }
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    .rounded-3 {
        border-radius: 0.5rem !important;
    }
    .last-message {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
        line-height: 1.4;
        margin-right: 5px; /* مسافة بين الرسالة وعلامة القراءة */
    }
    .conversation-link .card-body {
        padding: 0.75rem !important;
    }
    .message-status-container {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-shrink: 0;
    }
    .unread-badge-container {
        position: relative;
        display: inline-block;
    }
    .unread-badge {
        position: absolute;
        top: -8px;
        right: -8px;
    }
</style>
<div class="container py-4" style="max-width: 800px;">
    <!-- قائمة المحادثات -->
    <div class="conversations-section mb-5 p-3 bg-white rounded-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h2 class="fw-bold mb-0" style="color: #2c3e50;">المحادثات النشطة</h2>
                <p class="text-muted small mb-0">إدارة جميع محادثاتك في مكان واحد</p>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $conversations->total() }} محادثة</span>
        </div>
        
        <div id="conversations-list">
            @forelse($conversations as $conversation)
                @php
                    $other = $conversation->otherUser(Auth::user()->id);
                    $last = $conversation->lastMessage;
                    $unreadCount = $conversation->unreadMessagesCountForReciever();
                    $timestamp = $last ? strtotime($last->created_at) : null;
                @endphp
                <a href="{{ route('chat.open', $other) }}" class="text-decoration-none text-dark conversation-link" data-conversation-id="{{ $conversation->id }}">
                    <div class="card mb-3 border-0 bg-hover-light">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3" style="min-width: 0;">
                                    <div class="unread-badge-container flex-shrink-0">
                                        <div class="avatar bg-gradient-primary text-white d-flex justify-content-center align-items-center rounded-circle" style="width:50px;height:50px;">
                                            <span class="fw-bold fs-5">{{ mb_substr($other->name, 0, 1) }}</span>
                                        </div>
                                        @if($unreadCount > 0)
                                            <span class="badge rounded-pill bg-danger unread-badge">
                                                {{ $unreadCount }}
                                                <span class="visually-hidden">رسائل غير مقروءة</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-semibold text-truncate">{{ $other->name }}</h6>
                                            <span class="small text-muted flex-shrink-0 ps-2 last-message-time" data-timestamp="{{ $timestamp ?? '' }}">
                                                {{ $last ? $last->created_at->diffForHumans() : '' }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 text-muted small last-message" style="max-width: 70%;">
                                                @if($last)
                                                    @if($last->sender_id == Auth::user()->id)
                                                        <span class="text-primary">أنت:</span>
                                                    @endif
                                                    {{ $last->message }}
                                                @else
                                                    <span class="text-muted">لا توجد رسائل بعد</span>
                                                @endif
                                            </p>
                                            <div class="message-status-container">
                                                @if($last && $last->sender_id == Auth::user()->id)
                                                    <i class="fas fa-check{{ $last->is_read ? '-double text-info' : '' }}"></i>
                                                @endif
                                                @if($last && !$last->is_read && $last->sender_id != Auth::user()->id)
                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 new-message-indicator">جديد</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state text-center py-5 bg-light rounded-3">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted mb-2">لا توجد محادثات حتى الآن</h5>
                    <p class="text-muted small mb-0">إبدأ محادثة جديدة من قائمة المستخدمين بالأسفل</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- قائمة المستخدمين -->
    <div class="users-section p-3 bg-white rounded-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h2 class="fw-bold mb-0" style="color: #2c3e50;">جميع المستخدمين</h2>
                <p class="text-muted small mb-0">اختر مستخدم لبدء محادثة جديدة</p>
            </div>
            <span class="badge bg-success rounded-pill px-3 py-2">{{ $users->count() - 1 }} مستخدم</span>
        </div>
        
        <div class="row g-3">
            @foreach($users as $userItem)
                @if($userItem->id !== Auth::user()->id)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between p-3">
                                <div class="d-flex align-items-center gap-3" style="min-width: 0;">
                                    <div class="avatar bg-gradient-success text-white d-flex justify-content-center align-items-center rounded-circle flex-shrink-0" style="width:50px;height:50px;">
                                        <span class="fw-bold fs-5">{{ mb_substr($userItem->name, 0, 1) }}</span>
                                    </div>
                                    <div style="min-width: 0;">
                                        <h6 class="mb-0 fw-semibold text-truncate">{{ $userItem->name }}</h6>
                                        <small class="text-muted text-truncate d-block" style="max-width: 150px;">{{ $userItem->email }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('chat.open', $userItem) }}" class="btn btn-sm btn-primary rounded-pill px-3 flex-shrink-0">
                                    <i class="fas fa-paper-plane me-1"></i> محادثة
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Pagination -->
    @if($conversations->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            <nav aria-label="Page navigation">
                {{ $conversations->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    @endif
</div>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const PUSHER_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
        const PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

        // تفعيل Pusher
        Pusher.logToConsole = true;

        const pusher = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            forceTLS: true
        });

        const unreadChannel = pusher.subscribe('unread-messages-channel');
        const readChannel = pusher.subscribe('message-read-channel');

        // تحديث وقت الرسائل
        function updateMessageTimes() {
            const timeElements = document.querySelectorAll('.last-message-time[data-timestamp]');
            timeElements.forEach(el => {
                const sentTime = parseInt(el.getAttribute('data-timestamp')) * 1000;
                const diffSeconds = Math.floor((Date.now() - sentTime) / 1000);

                let timeText = "الآن";
                if (diffSeconds > 0) {
                    if (diffSeconds < 60) {
                        timeText = `منذ ${diffSeconds} ثوان`;
                    } else if (diffSeconds < 3600) {
                        const minutes = Math.floor(diffSeconds / 60);
                        timeText = `منذ ${minutes} دقائق`;
                    } else if (diffSeconds < 86400) {
                        const hours = Math.floor(diffSeconds / 3600);
                        timeText = `منذ ${hours} ساعات`;
                    } else {
                        const days = Math.floor(diffSeconds / 86400);
                        timeText = `منذ ${days} أيام`;
                    }
                }
                el.textContent = timeText;
            });
        }

        setInterval(updateMessageTimes, 5000);
        updateMessageTimes();

        // عند استلام رسالة جديدة
        unreadChannel.bind('unread-updated', function (data) {
            const conversationId = data.conversationId;
            const lastMessage = data.lastMessage;
            const unreadCount = data.unreadCount;
            const lastMessageTime = data.messageTime;
            const timestamp = data.timestamp || Math.floor(Date.now() / 1000);
            const isSender = data.isSender || false;
            const isRead = data.isRead || false;

            const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            if (!conversationElement) return;

            // تحديث نص الرسالة
            const lastMsgEl = conversationElement.querySelector('.last-message');
            if (lastMsgEl) {
                lastMsgEl.innerHTML = isSender ? `<span class="text-primary">أنت:</span> ${lastMessage}` : lastMessage;
            }

            // تحديث وقت الرسالة
            const timeEl = conversationElement.querySelector('.last-message-time');
            if (timeEl) {
                timeEl.setAttribute('data-timestamp', timestamp);
                timeEl.textContent = lastMessageTime || 'الآن';
            }

            // تحديث عدد الرسائل غير المقروءة
            const badgeContainer = conversationElement.querySelector('.unread-badge-container');
            let badge = conversationElement.querySelector('.unread-badge');
            
            if (unreadCount > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge rounded-pill bg-danger unread-badge';
                    badge.innerHTML = `${unreadCount} <span class="visually-hidden">رسائل غير مقروءة</span>`;
                    badgeContainer.appendChild(badge);
                } else {
                    badge.textContent = unreadCount;
                }
            } else if (badge) {
                badge.remove();
            }

            // تحديث علامة القراءة
            const statusContainer = conversationElement.querySelector('.message-status-container');
            if (statusContainer) {
                // إزالة العناصر الحالية
                statusContainer.innerHTML = '';

                if (isSender) {
                    const readIcon = document.createElement('i');
                    readIcon.className = isRead ? 'fas fa-check-double text-info' : 'fas fa-check';
                    statusContainer.appendChild(readIcon);
                } else if (!isRead) {
                    const newIndicator = document.createElement('span');
                    newIndicator.className = 'badge bg-warning text-dark rounded-pill px-2 py-1 new-message-indicator';
                    newIndicator.textContent = 'جديد';
                    statusContainer.appendChild(newIndicator);
                }
            }
        });

        // عند استلام حدث قراءة الرسالة
        readChannel.bind('message-read', function (data) {
            const conversationId = data.conversationId;
            const messageId = data.messageId;

            const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            if (!conversationElement) return;

            // تحديث علامة القراءة
            const statusContainer = conversationElement.querySelector('.message-status-container');
            if (statusContainer) {
                const icons = statusContainer.querySelectorAll('i');
                icons.forEach(icon => {
                    icon.className = 'fas fa-check-double text-info';
                });

                const newIndicator = statusContainer.querySelector('.new-message-indicator');
                if (newIndicator) {
                    newIndicator.remove();
                }
            }

            // تحديث عدد الرسائل غير المقروءة
            const badge = conversationElement.querySelector('.unread-badge');
            if (badge) {
                badge.remove();
            }
        });

        // عرض الرسالة كاملة عند التمرير فوقها
        document.querySelectorAll('.last-message').forEach(el => {
            el.addEventListener('mouseover', function() {
                this.style.whiteSpace = 'normal';
                this.style.overflow = 'visible';
                this.style.textOverflow = 'clip';
                this.style.webkitLineClamp = 'unset';
            });
            
            el.addEventListener('mouseout', function() {
                this.style.whiteSpace = 'normal';
                this.style.overflow = 'hidden';
                this.style.textOverflow = 'ellipsis';
                this.style.webkitLineClamp = '2';
            });
        });
    });
</script>
@endsection