<span class="nav-message-link position-relative d-inline-flex align-items-center">
    <i class="fas fa-comments"></i>
    <span class="ms-1">{{ $label ?? 'Messages' }}</span>
    <span id="message-notification-badge"
          class="message-notification-badge {{ ($count ?? 0) > 0 ? '' : 'd-none' }}"
          data-count="{{ $count ?? 0 }}">
        {{ ($count ?? 0) > 99 ? '99+' : ($count ?? 0) }}
    </span>
</span>
