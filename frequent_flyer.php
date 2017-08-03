<?php
include("search.php");
search_flights("flyer","","","","",$_GET['user'],check_airline());
include("footer.php");
?>