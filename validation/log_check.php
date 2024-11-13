<?php

session_start();

if (isset($_SESSION['loggedin'])) {
	if ($_SESSION['type'] == 'user') {
    header('Location: home.php');
    exit;
  }

  if ($_SESSION['type'] == 'coach') {
    header('Location: coach_dashboard.php');
    exit;
  }

  if ($_SESSION['type'] == 'player') {
    header('Location: player_dashboard.php');
    exit;
  }
}