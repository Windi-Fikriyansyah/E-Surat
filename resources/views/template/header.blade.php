<header class="mb-3">
    <nav class="navbar navbar-expand navbar-light navbar-top py-2 px-3 bg-white rounded shadow-sm">
        <div class="container-fluid px-0">
            <!-- Burger button untuk mobile / toggle sidebar -->
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3 text-primary"></i>
            </a>

            <!-- Bagian Kanan: Notifikasi & Info User -->
            <div class="ms-auto d-flex align-items-center gap-3">
                @auth
                    @php
                        $userNotifications = Auth::user()->notifications()->take(7)->get();
                        $unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
                    @endphp

                    <!-- Lonceng Notifikasi Aplikasi -->
                    <div class="dropdown" id="dropdownNotificationWrapper">
                        <a href="#" class="btn btn-light-secondary position-relative p-2 rounded-circle shadow-sm" data-bs-toggle="dropdown" aria-expanded="false" title="Pemberitahuan">
                            <i class="bi bi-bell-fill fs-5 text-dark"></i>
                            @if ($unreadNotificationsCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="badgeNotifCount">
                                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                                    <span class="visually-hidden">notifikasi belum dibaca</span>
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" style="min-width: 320px; max-width: 380px;">
                            <li class="p-3 bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
                                <div class="fw-bold">
                                    <i class="bi bi-bell me-1"></i> Notifikasi
                                </div>
                                @if ($unreadNotificationsCount > 0)
                                    <span class="badge bg-white text-primary small">{{ $unreadNotificationsCount }} Baru</span>
                                @endif
                            </li>

                            <div style="max-height: 350px; overflow-y: auto;">
                                @forelse ($userNotifications as $notif)
                                    @php
                                        $data = $notif->data;
                                        $isUnread = is_null($notif->read_at);
                                    @endphp
                                    <li class="border-bottom {{ $isUnread ? 'bg-light' : 'bg-white' }}">
                                        <a href="{{ route('notifications.read', $notif->id) }}" class="dropdown-item p-3 text-wrap d-flex align-items-start">
                                            <div class="me-3 mt-1">
                                                <span class="badge {{ $isUnread ? 'bg-primary' : 'bg-secondary' }} p-2 rounded-circle">
                                                    <i class="bi bi-envelope-fill text-white"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="small text-dark">{{ $data['title'] ?? 'Pemberitahuan Surat' }}</strong>
                                                    @if ($isUnread)
                                                        <span class="badge bg-danger rounded-pill" style="font-size: 8px;">Baru</span>
                                                    @endif
                                                </div>
                                                <p class="mb-1 text-muted small lh-sm">
                                                    {{ $data['message'] ?? 'Surat masuk baru telah ditujukan kepada Anda.' }}
                                                </p>
                                                <small class="text-primary font-monospace" style="font-size: 11px;">
                                                    <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="p-4 text-center text-muted">
                                        <i class="bi bi-bell-slash fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        <p class="small mb-0">Belum ada notifikasi baru.</p>
                                    </li>
                                @endforelse
                            </div>

                            @if ($unreadNotificationsCount > 0)
                                <li class="p-2 text-center bg-light rounded-bottom border-top">
                                    <button type="button" class="btn btn-link btn-sm text-decoration-none text-primary fw-bold" id="btnMarkAllNotifRead">
                                        <i class="bi bi-check2-all me-1"></i> Tandai Semua Sudah Dibaca
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Profil User -->
                    <div class="d-flex align-items-center">
                        <div class="text-end me-2 d-none d-md-block">
                            <span class="fw-bold d-block text-dark small">{{ Auth::user()->name }}</span>
                            <span class="badge bg-light text-primary border small" style="font-size: 10px;">{{ Auth::user()->role }}</span>
                        </div>
                        <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center fw-bold rounded-circle shadow-sm" style="width: 38px; height: 38px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
</header>

@push('js')
<script>
$(document).ready(function() {
    $('#btnMarkAllNotifRead').on('click', function(e) {
        e.preventDefault();
        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

        $.ajax({
            url: "{{ route('notifications.read-all') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $('#badgeNotifCount').remove();
                    btn.closest('li').remove();
                    $('#dropdownNotificationWrapper .bg-light').removeClass('bg-light').addClass('bg-white');
                    $('#dropdownNotificationWrapper .badge.bg-danger').remove();
                    $('#dropdownNotificationWrapper .badge.bg-primary').removeClass('bg-primary').addClass('bg-secondary');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check2-all me-1"></i> Tandai Semua Sudah Dibaca');
            }
        });
    });
});
</script>
@endpush
