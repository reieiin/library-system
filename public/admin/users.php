<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/models/admin/UsersModel.php';
require_once __DIR__ . '/../../app/controllers/admin/UsersController.php';

adminHandleUserAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

?>
<?php

$roles = adminGetRoles($conn);
$users = adminGetUsers($conn);
$totalUsers = adminGetUserCount($conn);
$totalRoles = adminGetRoleCount($conn);
$latestSignupLabel = adminGetLatestSignupLabel($conn);
$addUserError = $_SESSION['add_user_error'] ?? '';
$addUserOld = $_SESSION['add_user_old'] ?? [];
unset($_SESSION['add_user_error'], $_SESSION['add_user_old']);
?>

<div class="pagetitle">
    <h1>Users</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Accounts</div>
                    <h2 class="admin-hero-title">Manage Users</h2>
                    <p class="admin-hero-text">Manage user accounts and roles.</p>
                    <div class="admin-hero-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card">
                            <div class="admin-stat-label">Total Users</div>
                            <div class="admin-stat-value"><?php echo number_format($totalUsers); ?></div>
                            <div class="admin-stat-note">Registered users</div>
                        </div>
                        
                        <div class="admin-stat-card">
                            <div class="admin-stat-label">Latest Signup</div>
                            <div class="admin-stat-value"><?php echo htmlspecialchars($latestSignupLabel); ?></div>
                            <div class="admin-stat-note">Most recent account</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-panel admin-table-card">
        <div class="card-body">
            <div class="admin-panel-head mb-0">
                <div>
                    <h5 class="admin-panel-title">User Directory</h5>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus me-1"></i>Add User
                </button>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)) : ?>
                                    <?php foreach ($users as $user) : ?>
                                        <?php $isCurrentSessionUser = (int) ($_SESSION['authUser']['user_id'] ?? $_SESSION['user_id'] ?? 0) === (int) ($user['user_id'] ?? 0); ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                </div>
                                                <small class="text-muted"><?php echo htmlspecialchars($user['uuid']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td>
                                                <span class="badge <?php echo roleBadgeClass($user['role_name']); ?>"><?php echo htmlspecialchars($user['role_name']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars(formatDisplayDate($user['created_at'], true)); ?></td>
                                            <td class="text-end">
                                                <div class="action-controls">
                                                    <button type="button" class="btn btn-outline-primary action-btn edit-user-btn" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user-uuid="<?php echo htmlspecialchars($user['uuid'], ENT_QUOTES); ?>" data-first-name="<?php echo htmlspecialchars($user['first_name'], ENT_QUOTES); ?>" data-last-name="<?php echo htmlspecialchars($user['last_name'], ENT_QUOTES); ?>" data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>" data-username="<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>" data-street="<?php echo htmlspecialchars($user['street'], ENT_QUOTES); ?>" data-barangay="<?php echo htmlspecialchars($user['barangay'], ENT_QUOTES); ?>" data-city="<?php echo htmlspecialchars($user['city'], ENT_QUOTES); ?>" data-role-id="<?php echo htmlspecialchars($user['role_id']); ?>">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>

                                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                                                        method="post" class="m-0">
                                                        <input type="hidden" name="user_action" value="delete">
                                                        <input type="hidden" name="user_id"
                                                            value="<?php echo htmlspecialchars((string) $user['user_id'], ENT_QUOTES); ?>">
                                                        <button type="submit" class="btn btn-outline-danger action-btn"
                                                            <?php echo $isCurrentSessionUser ? 'disabled title="You cannot delete your own account."' : 'onclick="return confirm(\'Delete this user?\');"'; ?>>
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="user_action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($addUserError !== '') : ?>
                        <div class="alert alert-danger mb-3" role="alert">
                            <?php echo htmlspecialchars($addUserError); ?>
                        </div>
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars((string) ($addUserOld['first_name'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars((string) ($addUserOld['last_name'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars((string) ($addUserOld['email'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars((string) ($addUserOld['username'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role_id" class="form-select" required>
                                <option value="">Select role</option>
                                <?php foreach ($roles as $role) : ?>
                                    <option value="<?php echo htmlspecialchars($role['role_id']); ?>" <?php echo ((string) ($addUserOld['role_id'] ?? '') === (string) $role['role_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['role_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Street</label>
                            <input type="text" name="street" class="form-control" value="<?php echo htmlspecialchars((string) ($addUserOld['street'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Barangay</label>
                            <input type="text" name="barangay" class="form-control" value="<?php echo htmlspecialchars((string) ($addUserOld['barangay'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars((string) ($addUserOld['city'] ?? '')); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="user_action" value="update">
                <input type="hidden" name="user_uuid" id="edit_user_uuid">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Leave blank to keep current password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role_id" id="edit_role_id" class="form-select" required>
                                <option value="">Select role</option>
                                <?php foreach ($roles as $role) : ?>
                                    <option value="<?php echo htmlspecialchars($role['role_id']); ?>">
                                        <?php echo htmlspecialchars($role['role_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="edit_role_id_hidden" value="">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Street</label>
                            <input type="text" name="street" id="edit_street" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Barangay</label>
                            <input type="text" name="barangay" id="edit_barangay" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" id="edit_city" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editButtons = document.querySelectorAll('.edit-user-btn');
        const shouldOpenAddModal = <?php echo $addUserError !== '' ? 'true' : 'false'; ?>;

        if (shouldOpenAddModal) {
            const addUserModalElement = document.getElementById('addUserModal');

            if (addUserModalElement) {
                const addUserModal = new bootstrap.Modal(addUserModalElement);
                addUserModal.show();
            }
        }

        editButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const currentSessionUserId = <?php echo (int) ($_SESSION['authUser']['user_id'] ?? $_SESSION['user_id'] ?? 0); ?>;
                const selectedUserId = parseInt(button.closest('tr')?.querySelector('input[name="user_id"]')?.value || '0', 10);
                const isCurrentSessionUser = currentSessionUserId > 0 && currentSessionUserId === selectedUserId;

                document.getElementById('edit_user_uuid').value = button.dataset.userUuid || '';
                document.getElementById('edit_first_name').value = button.dataset.firstName || '';
                document.getElementById('edit_last_name').value = button.dataset.lastName || '';
                document.getElementById('edit_email').value = button.dataset.email || '';
                document.getElementById('edit_username').value = button.dataset.username || '';
                document.getElementById('edit_street').value = button.dataset.street || '';
                document.getElementById('edit_barangay').value = button.dataset.barangay || '';
                document.getElementById('edit_city').value = button.dataset.city || '';
                const roleSelect = document.getElementById('edit_role_id');
                const roleHidden = document.getElementById('edit_role_id_hidden');
                const roleValue = button.dataset.roleId || '';

                roleSelect.value = roleValue;
                roleHidden.value = roleValue;
                roleSelect.disabled = isCurrentSessionUser;

                if (isCurrentSessionUser) {
                    roleSelect.removeAttribute('name');
                    roleHidden.setAttribute('name', 'role_id');
                } else {
                    roleSelect.setAttribute('name', 'role_id');
                    roleHidden.removeAttribute('name');
                }

                if (isCurrentSessionUser) {
                    roleSelect.title = 'You cannot change your own role while logged in.';
                } else {
                    roleSelect.removeAttribute('title');
                }
            });
        });
    });
</script>

<?php
include('../includes/footer.php');
?>