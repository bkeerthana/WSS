<?php
/* Check if user selected a theme */
if(isset($_GET['theme'])){
    $theme = $_GET['theme'];

    // store theme in cookie for 1 hour
    setcookie("theme", $theme, time()+3600, "/");

    // reload page so cookie takes effect
    header("Location: theme_demo.php");
    exit();
}

/* Read theme from cookie */
$theme = $_COOKIE['theme'] ?? "light";
?>
<!DOCTYPE html>
<html>
<head>
<title>Theme Demo</title>

<style>
body{
    font-family: Arial;
    padding: 30px;
}

/* Light theme */
.light{
    background-color: white;
    color: black;
}

/* Dark theme */
.dark{
    background-color: 22;
    color: white;
}

button{
    padding:10px 15px;
    margin:10px;
}
</style>

</head>

<body class="<?php echo $theme; ?>">

<h2>Theme Preference Using Cookies</h2>

<p>Current Theme: <b><?php echo $theme; ?></b></p>

<button onclick="location.href='?theme=light'">Light Theme</button>

<button onclick="location.href='?theme=dark'">Dark Theme</button>

<hr>

<h3>Cookie Values</h3>

<?php
foreach($_COOKIE as $key => $value){
    echo "<p><b>$key</b> = $value</p>";
}
?>

</body>
</html>