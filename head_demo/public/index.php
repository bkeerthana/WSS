<?php
// Simple front controller for the demo app.
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Stamp & Coin Collection Tracker — HTTP HEAD Demo</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <header class="container">
    <h1>Stamp & Coin Collection Tracker</h1>
    <p class="sub">
      Manage your collection (list, search, add, edit, delete) and demonstrate <strong>HTTP HEAD</strong> safely.
    </p>
  </header>

  <main class="container grid">
    <section class="card">
      <h2>Add / Edit Item</h2>
      <form id="itemForm">
        <input type="hidden" id="id" />

        <div class="row">
          <label>Type</label>
          <select id="type" required>
            <option value="stamp">Stamp</option>
            <option value="coin">Coin</option>
          </select>
        </div>

        <div class="row">
          <label>Name / Title</label>
          <input id="name" type="text" placeholder="e.g., Mahatma Gandhi commemorative" required />
        </div>

        <div class="row">
          <label>Country</label>
          <input id="country" type="text" placeholder="e.g., India" />
        </div>

        <div class="row">
          <label>Year</label>
          <input id="year" type="number" min="1500" max="2100" placeholder="e.g., 2019" />
        </div>

        <div class="row">
          <label>Denomination</label>
          <input id="denomination" type="text" placeholder="e.g., ₹5 / 1 cent" />
        </div>

        <div class="row">
          <label>Condition</label>
          <select id="condition">
            <option value="mint">Mint</option>
            <option value="fine">Fine</option>
            <option value="good">Good</option>
            <option value="fair">Fair</option>
          </select>
        </div>

        <div class="row">
          <label>Acquired Date</label>
          <input id="acquired_date" type="date" />
        </div>

        <div class="row">
          <label>Estimated Value</label>
          <input id="value_estimate" type="number" step="0.01" min="0" placeholder="e.g., 250.00" />
        </div>

        <div class="row">
          <label>Notes</label>
          <textarea id="notes" rows="3" placeholder="Any special details..."></textarea>
        </div>

        <div class="actions">
          <button type="submit" class="primary" id="saveBtn">Save Item</button>
          <button type="button" id="resetBtn">Reset</button>
        </div>
      </form>
    </section>

    <section class="card">
      <h2>Search & Filters</h2>
      <div class="row">
        <label>Keyword</label>
        <input id="q" type="text" placeholder="Search name, notes, denomination..." />
      </div>

      <div class="row">
        <label>Type</label>
        <select id="filterType">
          <option value="">All</option>
          <option value="stamp">Stamp</option>
          <option value="coin">Coin</option>
        </select>
      </div>

      <div class="row">
        <label>Country</label>
        <input id="filterCountry" type="text" placeholder="e.g., India" />
      </div>

      <div class="row">
        <label>Year</label>
        <input id="filterYear" type="number" min="1500" max="2100" placeholder="e.g., 1980" />
      </div>

      <div class="actions">
        <button type="button" class="primary" id="searchBtn">Search</button>
        <button type="button" id="clearFiltersBtn">Clear</button>
      </div>

      <hr />

      <h3>HTTP HEAD Demo</h3>
      <p class="sub">
        HEAD returns <strong>headers only</strong> (no body). Use the buttons to compare.
      </p>
      <div class="actions">
        <button type="button" id="headItemsBtn">HEAD /api/items</button>
        <button type="button" id="getItemsBtn">GET /api/items</button>
        <button type="button" id="headExportBtn">HEAD /api/export (CSV)</button>
        <button type="button" id="getExportBtn">GET /api/export (CSV)</button>
      </div>

      <div class="mono" id="headResult" aria-live="polite"></div>
    </section>

    <section class="card span2">
      <div class="row between">
        <h2>Collection Items</h2>
        <div class="pill" id="countPill">0 items</div>
      </div>
      <div class="tableWrap">
        <table id="itemsTable">
          <thead>
            <tr>
              <th>Type</th>
              <th>Name</th>
              <th>Country</th>
              <th>Year</th>
              <th>Denomination</th>
              <th>Condition</th>
              <th>Value</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <p class="sub small">Tip: Use DevTools → Network to observe request method and response headers.</p>
    </section>
  </main>

  <footer class="container footer">
    <p>Demo app for teaching HTTP methods (starting with HEAD). Data is stored locally in a JSON file.</p>
  </footer>

  <script src="assets/app.js"></script>
</body>
</html>
