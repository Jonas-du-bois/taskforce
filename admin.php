<?php
require_once 'vendor/autoload.php';  // To load dependencies via Composer
require_once 'includes/functions.php'; // For custom functions

use M521\Taskforce\dbManager\DbManagerCRUD;

// Start session
session_start();

// Check if the user is logged in and if they are the administrator
if (!isset($_SESSION['user_connected']) || !$_SESSION['user_connected'] || $_SESSION['email_user'] !== 'jonas.du.bois@outlook.com') {
    header("Location: login.php"); // Redirect unauthorized users to the login page
    exit;
}

// Initialize the database manager
$dbManager = new DbManagerCRUD();

// Fetch all users from the database
$utilisateurs = $dbManager->getAllUsers();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/tab.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap icons -->
</head>

<body>
    <div class="d-flex">
        <!-- Include sidebar (navigation menu) -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main content -->
        <div class="main-content ms-auto col-md-9 col-lg-10 p-5">
            <h2 class="text-center mb-4 text-primary"><?php echo t('admin_heading'); ?></h2>

            <?php
            if (isset($_SESSION['successMessage'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['successMessage']) . '</div>';
                unset($_SESSION['successMessage']);
            }
            if (isset($_SESSION['errorMessage'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['errorMessage']) . '</div>';
                unset($_SESSION['errorMessage']);
            }
            ?>

            <!-- Check if there are users in the database -->
            <?php if (empty($utilisateurs)): ?>
                <div class="alert alert-warning mt-4" role="alert">
                    <p><?php echo t('no_users_found'); ?></p>
                </div>
            <?php else: ?>
                <!-- User table -->
                <div class="table-container p-3 mt-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <!-- Table headers -->
                                <th scope="col" style="width: 10%;"><?php echo t('id_label'); ?></th>
                                <th scope="col" style="width: 30%;"><?php echo t('email_label'); ?></th>
                                <th scope="col" style="width: 20%;"><?php echo t('phone_label'); ?></th>
                                <th scope="col" style="width: 20%;"><?php echo t('first_name_label'); ?></th>
                                <th scope="col" style="width: 15%;"><?php echo t('last_name_label'); ?></th>
                                <th scope="col" style="width: 15%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop to display each user -->
                            <?php foreach ($utilisateurs as $user): ?>
                                <tr>
                                    <!-- Display user information -->
                                    <td scope="row" class="fw-bold text-dark p-3"><?php echo htmlspecialchars($user->rendId()); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($user->rendEmail()); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($user->rendNoTel()); ?></td>
                                    <td class="text-dark"><?php echo htmlspecialchars($user->rendPrenom()); ?></td>
                                    <td class="text-dark"><?php echo htmlspecialchars($user->rendNom()); ?></td>
                                    <td>
                                        <form method="GET" action="delete_user.php" onsubmit="return confirmDeleteUser();">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user->rendId()); ?>">
                                            <!-- Button replaced with icon -->
                                            <button type="submit" class="btn btn-link text-danger p-0" style="font-size: 1.5rem;">
                                                <i class="bi bi-trash"></i> <!-- Trash icon -->
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    // JavaScript function for displaying a confirmation alert
    function confirmDeleteUser() {
        return confirm("<?php echo t('confirm_delete'); ?>");
    }
</script>
