<?php
/**
 * Example Users Configuration with Self-Elevation
 *
 * This is an example configuration showing different user roles.
 * Copy this file to users.php and customize with your own users.
 */

$users = [
    [
        'user_name' => 'admin',
        'password'  => 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', // "password"
        'role'      => 'admin', // Full admin - always elevated
    ],
    [
        'user_name' => 'elevated_user',
        'password'  => 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', // "password"
        'role'      => 'elevatable', // Can self-elevate to admin
    ],
    [
        'user_name' => 'standard_user',
        'password'  => 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', // "password"
        'role'      => 'user', // Standard user - no elevation
    ],
];

/**
 * ROLE EXPLANATION:
 *
 * 'admin' - Full administrator with permanent elevated privileges
 *           - Always has admin access
 *           - No need to elevate
 *
 * 'elevatable' - User who can temporarily elevate to admin
 *                - Can click the shield icon in navbar
 *                - Must re-enter password to elevate
 *                - Can de-elevate at any time
 *
 * 'user' - Standard user without elevation privileges
 *          - Cannot elevate to admin
 *          - No shield icon shown
 */
