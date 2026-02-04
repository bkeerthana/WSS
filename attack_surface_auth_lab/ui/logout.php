<?php require_once __DIR__ . '/../auth.php'; logout_user(); header('Location: /wss/attack_surface_auth_lab/ui/login.php?msg=' . urlencode('Logged out')); exit; ?>
