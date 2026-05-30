<?php
$basePath = '../';
require_once '../includes/db.php';
requireOwner();

$pageTitle = 'Kelola Admin — KostHub';
$pageTitleShort = 'Kelola Admin';

// Handle delete admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id) {
        if ($id === intval($_SESSION['user_id'])) {
            flashMsg("Anda tidak bisa menghapus akun Anda sendiri.", 'error');
        } else {
            // Check if user exists and is an admin
            $stmt_check = $db->prepare("SELECT username, role FROM users WHERE id = ?");
            $stmt_check->bind_param('i', $id);
            $stmt_check->execute();
            $u = $stmt_check->get_result()->fetch_assoc();
            
            if ($u) {
                if ($u['role'] !== 'admin') {
                    flashMsg("Hanya akun dengan role Admin yang dapat dihapus.", 'error');
                } else {
                    $username = $u['username'];
                    $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
                    $stmt->bind_param('i', $id);
                    if ($stmt->execute()) {
                        addLog($db, 'Admin dihapus', "Akun admin $username (ID: $id) dihapus oleh Owner", 'customer');
                        flashMsg("Akun admin $username berhasil dihapus.", 'success');
                    } else {
                        flashMsg("Gagal menghapus admin: " . $db->error, 'error');
                    }
                }
            } else {
                flashMsg("Akun tidak ditemukan.", 'error');
            }
        }
    }
    header('Location: admins.php');
    exit;
}

// Fetch all owners and admins
$staff = $db->query("SELECT id, username, role FROM users WHERE role IN ('owner', 'admin') ORDER BY role DESC, username")->fetch_all(MYSQLI_ASSOC);

require_once '../components/header.php';
require_once '../components/admin_sidebar.php';
require_once '../components/admin_topbar.php';
?>

<div>
  <div class="section-header">
    <div>
      <h2>Kelola Admin</h2>
      <p>Manajemen akun staff dan operator sistem</p>
    </div>
    <a href="admins_form.php" class="btn btn-primary" style="text-decoration: none;">
      <i class="bi bi-person-plus" style="font-size:14px"></i> Tambah Admin
    </a>
  </div>

  <?php showFlash(); ?>

  <div class="card">
    <div class="toolbar">
      <div class="search-wrap">
        <i class="bi bi-search search-icon" style="font-size:14px"></i>
        <input id="staff-search" placeholder="Cari username..." />
      </div>
    </div>
    
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="staff-tbody">
          <?php if (empty($staff)): ?>
            <tr><td colspan="4" style="text-align:center; color:var(--slate-muted)">Tidak ada data staff</td></tr>
          <?php else: ?>
            <?php foreach ($staff as $s): ?>
              <tr>
                <td><span style="font-family:'DM Mono',monospace; font-size:12px; color:var(--slate-muted)"><?= htmlspecialchars($s['id']) ?></span></td>
                <td><div style="font-weight:600"><?= htmlspecialchars($s['username']) ?></div></td>
                <td>
                  <?php if ($s['role'] === 'owner'): ?>
                    <span class="badge badge-purple" style="font-weight:600;">Owner</span>
                  <?php else: ?>
                    <span class="badge badge-amber" style="font-weight:600;">Admin</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display:flex; gap:6px">
                    <?php if ($s['role'] === 'admin'): ?>
                      <a href="admins_form.php?id=<?= urlencode($s['id']) ?>" class="btn btn-secondary btn-sm" title="Edit">
                        <i class="bi bi-pencil" style="font-size:12px"></i>
                      </a>
                      <?php if (intval($s['id']) !== intval($_SESSION['user_id'])): ?>
                        <form method="POST" action="admins.php" onsubmit="return confirm('Hapus akun admin <?= htmlspecialchars($s['username']) ?>?');" style="display:inline;">
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="id" value="<?= htmlspecialchars($s['id']) ?>">
                          <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                            <i class="bi bi-trash" style="font-size:12px"></i>
                          </button>
                        </form>
                      <?php endif; ?>
                    <?php else: ?>
                      <span style="color:var(--slate-muted); font-size:12px; font-style:italic">Akses Superadmin</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    
    <div class="pagination">
      <span class="info" id="staff-count">Menampilkan <?= count($staff) ?> akun staff</span>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('staff-search');
  if (searchInput) {
    searchInput.oninput = function() {
      const q = this.value.toLowerCase();
      let visibleCount = 0;
      const rows = document.querySelectorAll('#staff-tbody tr');
      rows.forEach(tr => {
        if (tr.cells.length < 2) return;
        const text = tr.textContent.toLowerCase();
        if (text.includes(q)) {
          tr.style.display = '';
          visibleCount++;
        } else {
          tr.style.display = 'none';
        }
      });
      document.getElementById('staff-count').textContent = `Menampilkan ${visibleCount} akun staff`;
    };
  }
});
</script>

<?php require_once '../components/footer_scripts.php'; ?>
