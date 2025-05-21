<?php
include '../include/connection.php';
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dash</title>
</head>
<body>
    <!-- logout -->
     <form  method="GET">
        <button type="submit" name="logout">Logout</button>
     </form>
</body>
</html>