<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                    <i class="fas fa-bell text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Notifiche
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Centro notifiche sistema WEBGRE
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="inline-flex items-center rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                <i class="fas fa-circle-notch mr-2"></i>
                <?= $unreadCount ?> non lette
            </span>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 dark:text-gray-400">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Notifiche</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Filters -->
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-800 dark:bg-gray-800/40">
    <div class="flex flex-wrap items-center gap-3">
        <a href="<?= $this->url('/notifications?filter=all') ?>"
           class="<?= $filter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' ?> rounded-xl px-4 py-2 text-sm font-medium transition-all hover:shadow-md">
            <i class="fas fa-list mr-2"></i>
            Tutte
        </a>
        <a href="<?= $this->url('/notifications?filter=unread') ?>"
           class="<?= $filter === 'unread' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' ?> rounded-xl px-4 py-2 text-sm font-medium transition-all hover:shadow-md">
            <i class="fas fa-envelope mr-2"></i>
            Non lette (<?= $unreadCount ?>)
        </a>
        <a href="<?= $this->url('/notifications?filter=read') ?>"
           class="<?= $filter === 'read' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' ?> rounded-xl px-4 py-2 text-sm font-medium transition-all hover:shadow-md">
            <i class="fas fa-envelope-open mr-2"></i>
            Lette
        </a>

        <div class="ml-auto flex gap-2">
            <?php if ($unreadCount > 0): ?>
            <button onclick="markAllAsRead()"
                    class="rounded-xl bg-green-500 px-4 py-2 text-sm font-medium text-white transition-all hover:bg-green-600 hover:shadow-md">
                <i class="fas fa-check-double mr-2"></i>
                Segna tutte come lette
            </button>
            <?php endif; ?>

            <button onclick="deleteAllRead()"
                    class="rounded-xl bg-red-500 px-4 py-2 text-sm font-medium text-white transition-all hover:bg-red-600 hover:shadow-md">
                <i class="fas fa-trash mr-2"></i>
                Elimina lette
            </button>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40">
    <?php if (empty($notifications)): ?>
    <!-- Empty State -->
    <div class="p-12 text-center">
        <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
            <i class="fas fa-bell-slash text-3xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
            Nessuna notifica
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Non hai notifiche in questo momento
        </p>
    </div>
    <?php else: ?>
    <!-- Notifications List -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        <?php foreach ($notifications as $notification): ?>
        <div class="notification-item flex items-start gap-4 p-5 transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 <?= $notification->isUnread() ? 'bg-blue-50 dark:bg-blue-900/10' : '' ?>"
             data-id="<?= $notification->id ?>">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl shadow-md
                    <?php
                    switch($notification->type) {
                        case 'success': echo 'bg-green-100 text-green-600 dark:bg-green-900/20'; break;
                        case 'warning': echo 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20'; break;
                        case 'error': echo 'bg-red-100 text-red-600 dark:bg-red-900/20'; break;
                        case 'repair': echo 'bg-cyan-100 text-cyan-600 dark:bg-cyan-900/20'; break;
                        case 'quality': echo 'bg-purple-100 text-purple-600 dark:bg-purple-900/20'; break;
                        case 'production': echo 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/20'; break;
                        case 'export': echo 'bg-orange-100 text-orange-600 dark:bg-orange-900/20'; break;
                        case 'system': echo 'bg-gray-100 text-gray-600 dark:bg-gray-900/20'; break;
                        default: echo 'bg-blue-100 text-blue-600 dark:bg-blue-900/20';
                    }
                    ?>">
                    <i class="<?= $notification->getDefaultIcon() ?> text-lg"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                        <?= htmlspecialchars($notification->title) ?>
                    </h4>
                    <?php if ($notification->isUnread()): ?>
                    <span class="flex-shrink-0 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <?= nl2br(htmlspecialchars($notification->message)) ?>
                </p>
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-500">
                    <span>
                        <i class="far fa-clock mr-1"></i>
                        <?= $notification->created_at->format('d/m/Y H:i') ?>
                    </span>
                    <?php if ($notification->link): ?>
                    <a href="<?= htmlspecialchars($notification->link) ?>"
                       class="text-blue-600 hover:underline dark:text-blue-400">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Vai al dettaglio
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2">
                <?php if ($notification->isUnread()): ?>
                <button onclick="markAsRead(<?= $notification->id ?>)"
                        class="rounded-lg bg-blue-500 p-2 text-white transition-all hover:bg-blue-600"
                        title="Segna come letta">
                    <i class="fas fa-check text-sm"></i>
                </button>
                <?php else: ?>
                <button onclick="markAsUnread(<?= $notification->id ?>)"
                        class="rounded-lg bg-gray-500 p-2 text-white transition-all hover:bg-gray-600"
                        title="Segna come non letta">
                    <i class="fas fa-envelope text-sm"></i>
                </button>
                <?php endif; ?>

                <button onclick="deleteNotification(<?= $notification->id ?>)"
                        class="rounded-lg bg-red-500 p-2 text-white transition-all hover:bg-red-600"
                        title="Elimina">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function markAsRead(notifId) {
    fetch(`<?= $this->url('/notifications/api/mark-read/') ?>${notifId}`, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAsUnread(notifId) {
    fetch(`<?= $this->url('/notifications/api/mark-unread/') ?>${notifId}`, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(notifId) {
    WebgreModals.confirmDelete(
        'Sei sicuro di voler eliminare questa notifica?',
        function() {
            fetch(`<?= $this->url('/notifications/api/delete/') ?>${notifId}`, {
                method: 'DELETE'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        },
        1
    );
}

function markAllAsRead() {
    fetch('<?= $this->url('/notifications/api/mark-all-read') ?>', {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteAllRead() {
    WebgreModals.confirm({
        title: 'Elimina Notifiche Lette',
        message: 'Sei sicuro di voler eliminare tutte le notifiche già lette?',
        confirmText: 'Elimina',
        cancelText: 'Annulla',
        type: 'danger',
        onConfirm: function() {
            fetch('<?= $this->url('/notifications/api/delete-all-read') ?>', {
                method: 'DELETE'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
}
</script>
