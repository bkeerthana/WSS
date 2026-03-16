<?php
date_default_timezone_set("Asia/Kolkata");

/* 1. User identity cookie */
setcookie("username", "student", time()+3600, "/");

/* 2. Preference cookie */
setcookie("theme", "dark", time()+3600, "/");

/* 3. Visit counter */
$visits = 1;
if(isset($_COOKIE['visits'])){
    $visits = (int)$_COOKIE['visits'] + 1;
}
setcookie("visits", $visits, time()+3600, "/");

/* 4. Authentication token */
setcookie("auth_token", "A93FJ2938KDF923", time()+3600, "/");

/* 5. Shopping cart data using JSON */
$cart = [
    "book" => 1,
    "mouse" => 2
];
setcookie("cart", json_encode($cart), time()+3600, "/");

/* 6. Language preference */
setcookie("language", "en", time()+3600, "/");
?>

<h2>Cookie Values Demo</h2>

<p>Cookies have been stored in your browser.</p>

<h3>Stored Cookies</h3>

<ul>
<li>username</li>
<li>theme</li>
<li>visits</li>
<li>auth_token</li>
<li>cart</li>
<li>language</li>
</ul>

<p><b>Updated visit count for this response:</b> <?php echo $visits; ?></p>

<p>Refresh the page to see visit count increase.</p>

<hr>

<h3>Reading Cookies received in this request</h3>

<?php
if(!empty($_COOKIE)){
    foreach($_COOKIE as $key => $value){
        echo "<p><b>$key</b> = " . htmlspecialchars($value) . "</p>";
    }
}else{
    echo "No cookies yet. Refresh page.";
}
?>