<?php

use App\Models\Notification;
use App\Models\User;

/**
 * Notifications Controller
 * Gestione sistema notifiche interno WEBGRE
 */
class NotificationsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Dashboard notifiche - Vista principale
     */
    public function index()
    {
        $this->logActivity('NOTIFICATIONS', 'VIEW_INDEX', 'Visualizzazione dashboard notifiche');

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->redirect($this->url('/login'));
            return;
        }

        $filter = $this->input('filter', 'all'); // all, unread, read

        $query = Notification::forUser($userId)->orderBy('created_at', 'desc');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        $notifications = $query->limit(100)->get();

        $data = [
            'pageTitle' => 'Notifiche',
            'notifications' => $notifications,
            'filter' => $filter,
            'unreadCount' => Notification::forUser($userId)->unread()->count()
        ];

        $this->render('notifications.index', $data);
    }

    /**
     * API: Recupera notifiche utente corrente
     * GET /notifications/api/list
     *
     * Params:
     *  - filter: all|unread|read (default: all)
     *  - limit: numero massimo notifiche (default: 20)
     *  - type: filtra per tipo notifica
     */
    public function apiList()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Non autenticato'], 401);
            return;
        }
        $filter = $this->input('filter', 'all');
        $limit = (int) $this->input('limit', 20);
        $type = $this->input('type');

        $query = Notification::forUser($userId)->orderBy('created_at', 'desc');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        if ($type) {
            $query->ofType($type);
        }

        $notifications = $query->limit($limit)->get();
        $unreadCount = Notification::forUser($userId)->unread()->count();

        $this->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->map(function($n) {
                    return [
                        'id' => $n->id,
                        'type' => $n->type,
                        'title' => $n->title,
                        'message' => $n->message,
                        'link' => $n->link,
                        'icon' => $n->getDefaultIcon(),
                        'color' => $n->getDefaultColor(),
                        'is_read' => $n->isRead(),
                        'read_at' => $n->read_at ? $n->read_at->format('Y-m-d H:i:s') : null,
                        'created_at' => $n->created_at->format('Y-m-d H:i:s'),
                        'time_ago' => $this->timeAgo($n->created_at)
                    ];
                })->toArray(),
                'unread_count' => $unreadCount
            ]
        ]);
    }

    /**
     * API: Recupera solo il conteggio notifiche non lette
     * GET /notifications/api/unread-count
     */
    public function apiUnreadCount()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Non autenticato'], 401);
            return;
        }
        $unreadCount = Notification::forUser($userId)->unread()->count();

        $this->json([
            'success' => true,
            'data' => ['unread_count' => $unreadCount]
        ]);
    }

    /**
     * API: Marca notifica come letta
     * POST /notifications/api/mark-read/:id
     */
    public function apiMarkRead($id)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Non autenticato'], 401);
            return;
        }

        $notification = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            $this->json(['success' => false, 'error' => 'Notifica non trovata'], 404);
            return;
        }

        $notification->markAsRead();

        $this->logActivity('NOTIFICATIONS', 'MARK_READ', "Notifica #{$id} marcata come letta");

        $this->json([
            'success' => true,
            'data' => [
                'message' => 'Notifica marcata come letta',
                'unread_count' => Notification::forUser($userId)->unread()->count()
            ]
        ]);
    }

    /**
     * API: Marca notifica come non letta
     * POST /notifications/api/mark-unread/:id
     */
    public function apiMarkUnread($id)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Non autenticato'], 401);
            return;
        }

        $notification = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            $this->json(['success' => false, 'error' => 'Notifica non trovata'], 404);
            return;
        }

        $notification->markAsUnread();

        $this->logActivity('NOTIFICATIONS', 'MARK_UNREAD', "Notifica #{$id} marcata come non letta");

        $this->json([
            'success' => true,
            'data' => [
                'message' => 'Notifica marcata come non letta',
                'unread_count' => Notification::forUser($userId)->unread()->count()
            ]
        ]);
    }

    /**
     * API: Marca tutte le notifiche come lette
     * POST /notifications/api/mark-all-read
     */
    public function apiMarkAllRead()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Non autenticato'], 401);
            return;
        }

        $updated = Notification::forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);

        $this->logActivity('NOTIFICATIONS', 'MARK_ALL_READ', "Marcate {$updated} notifiche come lette");

        $this->json([
            'success' => true,
            'data' => [
                'message' => "Tutte le notifiche ({$updated}) sono state marcate come lette",
                'updated_count' => $updated,
                'unread_count' => 0
            ]
        ]);
    }

    /**
     * API: Elimina notifica
     * DELETE /notifications/api/delete/:id
     */
    public function apiDelete($id)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Non autenticato'], 401);
            return;
        }

        $notification = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            $this->json(['success' => false, 'error' => 'Notifica non trovata'], 404);
            return;
        }

        $notification->delete();

        $this->logActivity('NOTIFICATIONS', 'DELETE', "Notifica #{$id} eliminata");

        $this->json([
            'success' => true,
            'data' => [
                'message' => 'Notifica eliminata',
                'unread_count' => Notification::forUser($userId)->unread()->count()
            ]
        ]);
    }

    /**
     * API: Elimina tutte le notifiche lette
     * DELETE /notifications/api/delete-all-read
     */
    public function apiDeleteAllRead()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Non autenticato'], 401);
            return;
        }

        $deleted = Notification::forUser($userId)
            ->read()
            ->delete();

        $this->logActivity('NOTIFICATIONS', 'DELETE_ALL_READ', "Eliminate {$deleted} notifiche lette");

        $this->json([
            'success' => true,
            'data' => [
                'message' => "Eliminate {$deleted} notifiche lette",
                'deleted_count' => $deleted
            ]
        ]);
    }

    /**
     * API: Crea nuova notifica (solo per admin o sistema)
     * POST /notifications/api/create
     *
     * Body params:
     *  - user_id: ID utente destinatario (o array di IDs)
     *  - type: tipo notifica
     *  - title: titolo
     *  - message: messaggio
     *  - link: link opzionale
     *  - icon: icona opzionale
     *  - color: colore opzionale
     */
    public function apiCreate()
    {
        // Solo admin possono creare notifiche via API
        if (!$this->isAdmin()) {
            $this->json(['success' => false, 'error' => 'Permesso negato'], 403);
            return;
        }

        $userId = $this->input('user_id');
        $type = $this->input('type', Notification::TYPE_INFO);
        $title = $this->input('title');
        $message = $this->input('message');
        $link = $this->input('link');
        $icon = $this->input('icon');
        $color = $this->input('color');

        // Validazione
        if (!$title || !$message) {
            $this->json(['success' => false, 'error' => 'Titolo e messaggio sono obbligatori'], 400);
            return;
        }

        // Se user_id è array, crea notifiche multiple
        if (is_array($userId)) {
            $count = Notification::notifyUsers($userId, $type, $title, $message, $link);

            $this->logActivity('NOTIFICATIONS', 'CREATE_MULTIPLE', "Create {$count} notifiche");

            $this->json([
                'success' => true,
                'data' => [
                    'message' => "Create {$count} notifiche",
                    'count' => $count
                ]
            ]);
            return;
        }

        // Se user_id non specificato, errore
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'user_id è obbligatorio'], 400);
            return;
        }

        // Crea singola notifica
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'icon' => $icon,
            'color' => $color
        ]);

        $this->logActivity('NOTIFICATIONS', 'CREATE', "Creata notifica #{$notification->id} per utente #{$userId}");

        $this->json([
            'success' => true,
            'data' => [
                'message' => 'Notifica creata',
                'notification' => $notification
            ]
        ]);
    }

    /**
     * API: Invia notifica a tutti gli utenti (solo admin)
     * POST /notifications/api/notify-all
     */
    public function apiNotifyAll()
    {
        if (!$this->isAdmin()) {
            $this->json(['success' => false, 'error' => 'Permesso negato'], 403);
            return;
        }

        $type = $this->input('type', Notification::TYPE_INFO);
        $title = $this->input('title');
        $message = $this->input('message');
        $link = $this->input('link');

        if (!$title || !$message) {
            $this->json(['success' => false, 'error' => 'Titolo e messaggio sono obbligatori'], 400);
            return;
        }

        $count = Notification::notifyAll($type, $title, $message, $link);

        $this->logActivity('NOTIFICATIONS', 'NOTIFY_ALL', "Notifica broadcast a {$count} utenti");

        $this->json([
            'success' => true,
            'data' => [
                'message' => "Notifica inviata a {$count} utenti",
                'count' => $count
            ]
        ]);
    }

    /**
     * API: Invia notifica a tutti gli admin (solo admin)
     * POST /notifications/api/notify-admins
     */
    public function apiNotifyAdmins()
    {
        if (!$this->isAdmin()) {
            $this->json(['success' => false, 'error' => 'Permesso negato'], 403);
            return;
        }

        $type = $this->input('type', Notification::TYPE_SYSTEM);
        $title = $this->input('title');
        $message = $this->input('message');
        $link = $this->input('link');

        if (!$title || !$message) {
            $this->json(['success' => false, 'error' => 'Titolo e messaggio sono obbligatori'], 400);
            return;
        }

        $count = Notification::notifyAdmins($type, $title, $message, $link);

        $this->logActivity('NOTIFICATIONS', 'NOTIFY_ADMINS', "Notifica a {$count} amministratori");

        $this->json([
            'success' => true,
            'data' => [
                'message' => "Notifica inviata a {$count} amministratori",
                'count' => $count
            ]
        ]);
    }

    /**
     * Helper: calcola "time ago" per notifiche
     */
    protected function timeAgo($datetime)
    {
        $now = time();
        $timestamp = strtotime($datetime);
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'proprio ora';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' minut' . ($minutes > 1 ? 'i' : 'o') . ' fa';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' or' . ($hours > 1 ? 'e' : 'a') . ' fa';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' giorn' . ($days > 1 ? 'i' : 'o') . ' fa';
        } else {
            return date('d/m/Y H:i', $timestamp);
        }
    }

}
