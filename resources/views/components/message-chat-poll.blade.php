@push('scripts')
<script>
(function () {
    const container = document.getElementById('chat-messages');
    if (!container) return;

    const pollUrl = @json($pollUrl);
    let lastMessageId = parseInt(container.dataset.lastMessageId || '0', 10);

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function appendMessage(message) {
        const emptyState = container.querySelector('.chat-empty-state');
        if (emptyState) emptyState.remove();

        const wrapper = document.createElement('div');
        wrapper.className = 'message mb-3 ' + (message.is_mine ? 'text-end' : 'text-start');
        wrapper.dataset.messageId = message.id;

        const bubbleClass = message.is_mine ? 'bg-primary text-white' : 'bg-light';
        const timeClass = message.is_mine ? 'text-white-50' : 'text-muted';
        const newBadge = (!message.is_mine && !message.read)
            ? '<span class="badge bg-success ms-1 message-new-indicator">New</span>'
            : '';

        wrapper.innerHTML =
            '<div class="d-inline-block ' + bubbleClass + ' p-3 rounded" style="max-width: 70%;">' +
                '<p class="mb-1">' + escapeHtml(message.message) + '</p>' +
                '<small class="' + timeClass + '">' + escapeHtml(message.created_at) + newBadge + '</small>' +
            '</div>';

        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
        lastMessageId = Math.max(lastMessageId, message.id);
    }

    function pollConversation() {
        const url = new URL(pollUrl, window.location.origin);
        url.searchParams.set('conversation', '1');
        url.searchParams.set('mark_read', '1');
        if (lastMessageId > 0) {
            url.searchParams.set('after_id', String(lastMessageId));
        }

        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(response => response.ok ? response.json() : null)
            .then(data => {
                if (!data) return;

                (data.messages || []).forEach(appendMessage);

                const badge = document.getElementById('message-notification-badge');
                if (badge) {
                    const count = parseInt(data.count || 0, 10);
                    if (count <= 0) {
                        badge.classList.add('d-none');
                        badge.textContent = '0';
                    } else {
                        badge.classList.remove('d-none');
                        badge.textContent = count > 99 ? '99+' : String(count);
                    }
                    badge.dataset.count = String(count);
                }
            })
            .catch(() => {});
    }

    container.scrollTop = container.scrollHeight;
    setInterval(pollConversation, 5000);
})();
</script>
@endpush
