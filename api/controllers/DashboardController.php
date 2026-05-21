<?php
// DashboardController — GET /api/dashboard

function handleDashboard($db, $method, $id, $input) {
    if ($method !== 'GET') { jsonOut(['error' => 'Method not allowed'], 405); }

    $rooms   = $db->query("SELECT * FROM rooms")->fetch_all(MYSQLI_ASSOC);
    $orders  = $db->query("SELECT * FROM orders")->fetch_all(MYSQLI_ASSOC);
    $repairs = $db->query("SELECT * FROM repairs")->fetch_all(MYSQLI_ASSOC);
    $logs    = $db->query("SELECT * FROM logs ORDER BY time DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

    $occupied     = count(array_filter($rooms, fn($r) => $r['status'] === 'occupied'));
    $empty        = count(array_filter($rooms, fn($r) => $r['status'] === 'empty'));
    $maint        = count(array_filter($rooms, fn($r) => $r['status'] === 'maintenance'));
    $pendingInv   = array_sum(array_map(fn($o) => $o['status'] === 'pending' ? $o['total'] : 0, $orders));
    $totalRev     = array_sum(array_map(fn($o) => $o['status'] === 'paid' ? $o['total'] : 0, $orders));
    $pendingOrders = count(array_filter($orders, fn($o) => $o['status'] === 'pending'));
    $activeRepairs = count(array_filter($repairs, fn($r) => $r['status'] !== 'done'));

    jsonOut([
        'stats' => compact('occupied', 'empty', 'maint', 'totalRev', 'pendingInv', 'pendingOrders', 'activeRepairs')
                   + ['maintenance' => $maint, 'totalRooms' => count($rooms)],
        'rooms'        => $rooms,
        'recentOrders' => array_slice($orders, 0, 4),
        'recentLogs'   => $logs,
    ]);
}
