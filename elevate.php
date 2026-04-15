<?php
/**
 * Self-Elevation Handler
 * Allows users with 'elevatable' role to temporarily elevate to admin
 */

/**
 * load required files
 */
require_once 'vendor/autoload.php';
require_once 'common.php';

/**
 * load the configuration file if readable
 */
if (is_file('config/config.php') && is_readable('config/config.php')) {
    require_once 'config/config.php';
} else {
    exit;
}

/**
 * load the file containing user accounts, if readable
 */
if (is_file('config/users.php') && is_readable('config/users.php')) {
    require_once 'config/users.php';
} else {
    exit;
}

/**
 * start session
 */
session_start();

/**
 * check if user is logged in
 */
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ' . dirname($_SERVER['REQUEST_URI']));
    exit;
}

/**
 * Handle elevation request
 */
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'elevate') {
        /**
         * Check if user has elevatable role
         */
        $user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';

        if ($user_role === 'elevatable' || $user_role === 'admin') {
            /**
             * Verify password again for security
             */
            if (isset($_POST['password']) && !empty($_POST['password'])) {
                $password = $_POST['password'];
                $password_hash = hash('sha512', $password);
                $user_name = $_SESSION['user_name'];

                /**
                 * Verify credentials
                 */
                $verified = false;
                if (!empty($users)) {
                    foreach ($users as $user) {
                        if ($user['user_name'] === $user_name && strtoupper($user['password']) === strtoupper($password_hash)) {
                            $verified = true;
                            break;
                        }
                    }
                }

                if ($verified) {
                    $_SESSION['is_elevated'] = true;
                    $_SESSION['elevation_time'] = time();
                    error_log('User ' . $user_name . ' elevated to admin');

                    returnJson([
                        'success' => true,
                        'message' => 'Successfully elevated to admin',
                    ]);
                } else {
                    error_log('Failed elevation attempt for user ' . $user_name . ' - incorrect password');

                    returnJson([
                        'success' => false,
                        'message' => 'Incorrect password',
                    ]);
                }
            } else {
                returnJson([
                    'success' => false,
                    'message' => 'Password required',
                ]);
            }
        } else {
            error_log('Failed elevation attempt for user ' . $_SESSION['user_name'] . ' - insufficient permissions');

            returnJson([
                'success' => false,
                'message' => 'You do not have permission to elevate',
            ]);
        }
    } elseif ($action === 'de-elevate') {
        $_SESSION['is_elevated'] = false;
        unset($_SESSION['elevation_time']);
        error_log('User ' . $_SESSION['user_name'] . ' de-elevated from admin');

        returnJson([
            'success' => true,
            'message' => 'Successfully de-elevated from admin',
        ]);
    } elseif ($action === 'check') {
        /**
         * Return current elevation status
         */
        $is_elevated = isset($_SESSION['is_elevated']) ? $_SESSION['is_elevated'] : false;
        $user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';
        $can_elevate = ($user_role === 'elevatable' || $user_role === 'admin');

        returnJson([
            'success' => true,
            'is_elevated' => $is_elevated,
            'can_elevate' => $can_elevate,
            'user_role' => $user_role,
        ]);
    }
} else {
    returnJson([
        'success' => false,
        'message' => 'Invalid request',
    ]);
}

exit;
