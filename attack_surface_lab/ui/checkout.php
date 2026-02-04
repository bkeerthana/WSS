<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_login();
header("Content-Type: text/html; charset=UTF-8");

$mode = lab_mode();
$u = current_user();

/**
 * Server-side product catalog (truth)
 */
$catalog = [
  "STMP_BASIC" => ["name" => "Stamp Album (Basic)", "price" => 499],
  "STMP_PRO"   => ["name" => "Stamp Album (Pro)",   "price" => 999],
];

$sku = $_GET["sku"] ?? "STMP_BASIC";
if (!isset($catalog[$sku])) $sku = "STMP_BASIC";

$product = $catalog[$sku];
$serverPrice = $product["price"];

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $postedSku   = (string)($_POST["sku"] ?? "");
  $postedQty   = (int)($_POST["qty"] ?? 1);
  $postedPrice = (int)($_POST["price"] ?? 0); // client-controlled (tamper target)
  $postedDisc  = (int)($_POST["discount"] ?? 0); // client-controlled (tamper target)

  if (!isset($catalog[$postedSku])) {
    $msg = "Invalid product.";
  } else {
    $truthPrice = (int)$catalog[$postedSku]["price"];
    $qty = max(1, min(10, $postedQty)); // basic bound

    if ($mode === "VULNERABLE") {
      // VULNERABLE: server trusts client-provided price & discount
      $final = max(0, ($postedPrice * $qty) - $postedDisc);
      $msg = "VULNERABLE: Charged amount (trusted client params) = ₹" . $final;
    } else {
      // HARDENED: server ignores client price/discount and computes truth
      $final = $truthPrice * $qty;
      $msg = "HARDENED: Charged amount (server computed) = ₹" . $final;
    }
  }
}
?>
<h3>Checkout (Parameter Tampering Demo)</h3>
<p>
  Mode: <b><?= htmlspecialchars($mode) ?></b> |
  <a href="../mode.php">Switch</a> |
  <a href="dashboard.php">Dashboard</a>
</p>

<form method="GET">
  <label>Product:
    <select name="sku">
      <?php foreach ($catalog as $k => $v): ?>
        <option value="<?= htmlspecialchars($k) ?>" <?= $k===$sku ? "selected" : "" ?>>
          <?= htmlspecialchars($v["name"]) ?> (₹<?= (int)$v["price"] ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  <button>Choose</button>
</form>

<hr>

<form method="POST">
  <p><b>Selected:</b> <?= htmlspecialchars($product["name"]) ?> | Server price: ₹<?= (int)$serverPrice ?></p>

  <input type="hidden" name="sku" value="<?= htmlspecialchars($sku, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Tamper targets -->
  <input type="hidden" name="price" value="<?= (int)$serverPrice ?>">
  <input type="hidden" name="discount" value="0">

  <label>Quantity:
    <input name="qty" value="1" style="width:80px">
  </label>

  <button>Pay</button>
</form>

<?php if ($msg): ?>
  <p style="margin-top:14px"><b><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></b></p>
<?php endif; ?>

<p style="color:#555">
  Teaching note: In VULNERABLE mode the server trusts hidden fields like <code>price</code> and <code>discount</code>.
  In HARDENED mode it recomputes from server-side catalog.
</p>
