<?php
require "config.php";
if(!isset($_SESSION['user_id'])||$_SESSION['role']!="admin"){die("403 Forbidden");}
$r=$pdo->query("SELECT username,page,COUNT(*) visits,SUM(duration_seconds) total,AVG(duration_seconds) avg FROM page_sessions WHERE duration_seconds IS NOT NULL GROUP BY username,page ORDER BY username,page")->fetchAll();
?>
<!doctype html><html><head><title>Page Report</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header><h1>Page Report</h1><nav>Page Visits • Duration</nav></header>
<div class="container">
  <div class="card">
    <h3>Summary</h3>
    <table>
      <tr><th>User</th><th>Page</th><th>Visits</th><th>Total (s)</th><th>Avg (s)</th></tr>
      <?php foreach($r as $row){ ?>
        <tr>
          <td><?=htmlspecialchars($row['username'])?></td>
          <td><?=htmlspecialchars($row['page'])?></td>
          <td><?=$row['visits']?></td>
          <td><?=$row['total']?></td>
          <td><?=round($row['avg'],1)?></td>
        </tr>
      <?php } ?>
    </table>
  </div>
  <a href="admin_home.php" class="button">Back</a>
</div>
<div class="footer">AAA Security Lab – Page Report</div>
</body></html>