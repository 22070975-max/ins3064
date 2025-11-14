<?php
// ===================== roles.php =====================
$roles = [
    'admin' => ['view_user', 'create_user', 'edit_user', 'delete_user'],
    'user' => ['view_user', 'edit_own_profile'],
    'guest' => ['view_user']
];

$user_roles = [
    1 => 'admin',
    2 => 'user',
    3 => 'guest'
];
?>

<?php
// ===================== permissions.php =====================
require 'roles.php';

function hasPermission($user_id, $permission) {
    global $user_roles, $roles;
    $role = $user_roles[$user_id] ?? 'guest';
    return in_array($permission, $roles[$role]);
}
?>

<?php
// ===================== session_access.php =====================
session_start();
require 'roles.php';

// Fake login for demo
$_SESSION['user_role'] = 'admin';

function checkAccess($required_permission) {
    global $roles;
    $role = $_SESSION['user_role'] ?? 'guest';
    return in_array($required_permission, $roles[$role]);
}
?>

<?php
// ===================== db_structure.sql =====================
/*
CREATE TABLE users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50),
  role_id INT
);

CREATE TABLE roles (
  role_id INT PRIMARY KEY AUTO_INCREMENT,
  role_name VARCHAR(50)
);

CREATE TABLE permissions (
  permission_id INT PRIMARY KEY AUTO_INCREMENT,
  permission_name VARCHAR(50)
);

CREATE TABLE role_permissions (
  role_id INT,
  permission_id INT
);
*/
?>

<?php
// ===================== get_user_permissions.php =====================
function getUserPermissions($user_id) {
    $conn = mysqli_connect("localhost", "user", "pass", "db");

    $sql = "SELECT p.permission_name
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            JOIN role_permissions rp ON r.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.permission_id
            WHERE u.user_id = $user_id";

    $result = mysqli_query($conn, $sql);
    $permissions = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $permissions[] = $row['permission_name'];
    }
    return $permissions;
}
?>

<?php
// ===================== check_permission.php =====================
require_once 'session_access.php';

function requirePermission($permission) {
    if (!checkAccess($permission)) {
        header("Location: unauthorized.php");
        exit();
    }
}
?>

<?php
// ===================== menu.php =====================
require 'session_access.php';
?>
<a href="index.php">Home</a>
<?php if (checkAccess('delete_user')): ?>
    <a href="delete.php">Delete User</a>
<?php endif; ?>
<?php if (checkAccess('edit_user')): ?>
    <a href="edit.php">Edit User</a>
<?php endif; ?>

<?php
// ===================== role_hierarchy.php =====================
/* roles table must include: role_inherit (INT) */

function getAllPermissions($role_id, $conn) {
    $permissions = [];

    while ($role_id) {
        $sql = "SELECT p.permission_name, r.role_inherit
                FROM roles r
                LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
                LEFT JOIN permissions p ON rp.permission_id = p.permission_id
                WHERE r.role_id = $role_id";

        $result = mysqli_query($conn, $sql);

        $next_role = null;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['permission_name']) {
                $permissions[] = $row['permission_name'];
            }
            $next_role = $row['role_inherit'];
        }
        $role_id = $next_role;
    }

    return array_unique($permissions);
}
?>

<?php
// ===================== manage_roles.php =====================
$conn = mysqli_connect("localhost", "user", "pass", "db");

function addPermissionToRole($role_id, $permission_id) {
    global $conn;
    $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES ($role_id, $permission_id)";
    mysqli_query($conn, $sql);
}

function removePermissionFromRole($role_id, $permission_id) {
    global $conn;
    $sql = "DELETE FROM role_permissions WHERE role_id = $role_id AND permission_id = $permission_id";
    mysqli_query($conn, $sql);
}
?>
