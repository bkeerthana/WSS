<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>JWT Login Lab</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.5; }
        .card { border: 1px solid #ccc; padding: 16px; margin-bottom: 16px; border-radius: 8px; }
        code { background: #f4f4f4; padding: 2px 4px; }
    </style>
</head>
<body>
    <h1>JWT Login Scenario Lab</h1>

    <div class="card">
        <h3>Users</h3>
        <p>
            Student: <code>student1 / student123</code><br>
            Faculty: <code>faculty1 / faculty123</code><br>
            Admin: <code>admin1 / admin123</code>
        </p>
    </div>

    <div class="card">
        <h3>Login</h3>
        <!--
        <form action="login.php" method="post">
            <label>Username</label><br>
            <input type="text" name="username" value="student1" required><br><br>

            <label>Password</label><br>
            <input type="password" name="password" value="student123" required><br><br>

            <button type="submit">Login</button>
        </form>
        -->

        <form action="login.php" method="post" autocomplete="off">

    <label>Username</label><br>
    <input type="text" name="username" placeholder="Enter username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" placeholder="Enter password" required><br><br>

    <button type="submit">Login</button>

        </form>
    </div>

    <div class="card">
        <h3>Goal of this lab</h3>
        <ol>
            <li>See authentication and authorization in a realistic login flow.</li>
            <li>Then change the code and observe the difference.</li>
            <li>Students should edit the marked LAB TASK lines one by one.</li>
        </ol>
    </div>
</body>
</html>
