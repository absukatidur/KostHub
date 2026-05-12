<?php
// AuthController — POST /api/auth/login, GET /api/auth/me, POST /api/auth/logout

function handleAuth($db, $method, $action, $input) {
    switch ($action) {
        case 'login':
            if ($method !== 'POST') jsonOut(['error' => 'Method not allowed'], 405);
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';
            if (!$username || !$password) jsonOut(['error' => 'Username dan password wajib diisi'], 400);

            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if (!$user || !password_verify($password, $user['password'])) {
                jsonOut(['error' => 'Username atau password salah'], 401);
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['customer_id'] = $user['customer_id'];

            // Get customer name if user role
            $name = $user['username'];
            if ($user['role'] === 'user' && $user['customer_id']) {
                $cs = $db->prepare("SELECT name FROM customers WHERE id = ?");
                $cs->bind_param('s', $user['customer_id']);
                $cs->execute();
                $cust = $cs->get_result()->fetch_assoc();
                if ($cust) $name = $cust['name'];
            }

            jsonOut([
                'success' => true,
                'role' => $user['role'],
                'username' => $user['username'],
                'name' => $name,
                'customer_id' => $user['customer_id']
            ]);
            break;

        case 'me':
            if ($method !== 'GET') jsonOut(['error' => 'Method not allowed'], 405);
            if (empty($_SESSION['user_id'])) jsonOut(['error' => 'Not authenticated'], 401);

            $name = $_SESSION['username'];
            if ($_SESSION['role'] === 'user' && $_SESSION['customer_id']) {
                $cs = $db->prepare("SELECT name FROM customers WHERE id = ?");
                $cs->bind_param('s', $_SESSION['customer_id']);
                $cs->execute();
                $cust = $cs->get_result()->fetch_assoc();
                if ($cust) $name = $cust['name'];
            }

            jsonOut([
                'role' => $_SESSION['role'],
                'username' => $_SESSION['username'],
                'name' => $name,
                'customer_id' => $_SESSION['customer_id'] ?? null
            ]);
            break;

        case 'logout':
            if ($method !== 'POST') jsonOut(['error' => 'Method not allowed'], 405);
            session_destroy();
            jsonOut(['success' => true]);
            break;

        default:
            jsonOut(['error' => 'Not found'], 404);
    }
}
