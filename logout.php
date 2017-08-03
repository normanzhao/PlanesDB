<?php
include("header.php");
session_destroy();
echo "<div id='logout_text'>Logging out...redirecting to main page in a few seconds...</div>";
echo '<meta http-equiv="refresh" content="3;url=main.php">';
include("footer.php");
?>
