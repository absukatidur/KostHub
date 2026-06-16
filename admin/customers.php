<?php
$basePath = '../';
require_once '../includes/db.php';
requireAdmin();

$pageTitle = 'Penghuni — KostHub';
$pageTitleShort = 'Penghuni';

// Handle delete customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        $stmt_cust = $db->prepare("SELECT name, room FROM customers WHERE id = ?");
        $stmt_cust->bind_param('s', $id);
        $stmt_cust->execute();
        $c = $stmt_cust->get_result()->fetch_assoc();
        if ($c) {
            $name = $c['name'];
            // If they are in a room, clear that room
            if (!empty($c['room'])) {
                $db->query("UPDATE rooms SET status = 'empty', tenant = '-', `until` = '-' WHERE id = '" . $db->real_escape_string($c['room']) . "'");
            }
            
            $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->bind_param('s', $id);
            if ($stmt->execute()) {
                // Delete linked user account
                $db->query("DELETE FROM users WHERE customer_id = '" . $db->real_escape_string($id) . "'");
                addLog($db, 'Penghuni dihapus', "$name ($id) dihapus", 'customer');
                flashMsg("Penghuni $name berhasil dihapus.", 'success');
            } else {
                flashMsg("Gagal menghapus customer: " . $db->error, 'error');
            }
        }
    }
    header('Location: customers.php');
    exit;
}

// Fetch all customers
$customers = $db->query("SELECT * FROM customers ORDER BY id")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Penghuni</h2>
      <p>Data penghuni kos</p>
    </div>
    <a href="customers_form.php" class="btn btn-primary btn-link">
      <i class="bi bi-person-plus" style="font-size: 14px;"></i> Tambah Penghuni
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" style="font-size: 14px;"></i>
        <input id="cust-search" placeholder="Cari nama, email, WA..." />
      </div>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email / WA</th>
            <th>Kamar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="cust-tbody">
          <?php if (empty($customers)): ?>
            <tr><td colspan="5" class="td-empty">Tidak ada data customer</td></tr>
          <?php else: ?>
            <?php foreach ($customers as $c): ?>
              <tr>
                <td><span class="td-mono"><?= htmlspecialchars($c['id']) ?></span></td>
                <td><div class="td-bold"><?= htmlspecialchars($c['name']) ?></div></td>
                <td>
                  <div class="text-accent"><?= htmlspecialchars($c['email']) ?></div>
                  <div class="text-sm text-muted"><?= htmlspecialchars($c['wa']) ?></div>
                </td>
                <td>
                  <?php if (!empty($c['room'])): ?>
                    <a href="rooms_detail.php?id=<?= urlencode($c['room']) ?>" class="badge badge-green btn-link" style="font-weight:600;"><?= htmlspecialchars($c['room']) ?></a>
                  <?php else: ?>
                    <span class="text-muted">Belum sewa</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="action-group">
                    <a href="customers_detail.php?id=<?= urlencode($c['id']) ?>" class="btn btn-secondary btn-sm" title="Detail">
                      <i class="bi bi-eye" class="text-sm"></i>
                    </a>
                    <a href="customers_form.php?id=<?= urlencode($c['id']) ?>" class="btn btn-secondary btn-sm" title="Edit">
                      <i class="bi bi-pencil" class="text-sm"></i>
                    </a>
                    <form method="POST" action="customers.php" onsubmit="return confirm('Hapus Penghuni <?= htmlspecialchars($c['name']) ?>? Data sewa dan akun user juga akan dihapus.');" class="inline-form">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($c['id']) ?>">
                      <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="bi bi-trash" class="text-sm"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    
    <div class="pagination">
      <span class="info" id="cust-count">Menampilkan <?= count($customers) ?> customer</span>
    </div>
  </div>
</div>

<script src="<?= $basePath ?? '' ?>assets/js/table-search.js?v=<?= time() ?>"></script>
<script>
initTableSearch('cust-search', '#cust-tbody tr', 'cust-count', 'Menampilkan {count} customer');
</script>

<?php require_once '../components/footer_scripts.php'; ?>
