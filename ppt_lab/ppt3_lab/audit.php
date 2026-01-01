<?php
require_once __DIR__ . '/db.php';
$r=db_app()->query("SELECT * FROM audit ORDER BY id DESC")->fetchAll();
?>
<h2>Audit</h2>
<table border=1>
<?php foreach($r as $a){?>
<tr><td><?=$a['ts']?></td><td><?=$a['actor']?></td><td><?=$a['event']?></td><td><?=$a['details']?></td></tr>
<?php }?>
</table>
