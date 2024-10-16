<?php
require_once 'session.php';

// Destroy the session
session_start();
session_destroy();

// Redirect to login page
header("Location: ../FRONTEND/pages/login.php");
exit();