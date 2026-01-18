const API_ITEMS = 'api/items.php';
const API_EXPORT = 'api/export.php?format=csv';

function el(id){return document.getElementById(id)}
function esc(s){return (s??'').toString().replace(/[&<>"']/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;"}[c]))}

async function apiFetch(url, opts={}){
  const res = await fetch(url, {
    headers: { 'Content-Type': 'application/json', ...(opts.headers||{}) },
    ...opts
  });
  return res;
}

function formToPayload(){
  return {
    id: el('id').value || undefined,
    type: el('type').value,
    name: el('name').value.trim(),
    country: el('country').value.trim(),
    year: el('year').value ? Number(el('year').value) : null,
    denomination: el('denomination').value.trim(),
    condition: el('condition').value,
    acquired_date: el('acquired_date').value || null,
    value_estimate: el('value_estimate').value ? Number(el('value_estimate').value) : null,
    notes: el('notes').value.trim()
  };
}

function fillForm(item){
  el('id').value = item.id;
  el('type').value = item.type;
  el('name').value = item.name || '';
  el('country').value = item.country || '';
  el('year').value = item.year ?? '';
  el('denomination').value = item.denomination || '';
  el('condition').value = item.condition || 'good';
  el('acquired_date').value = item.acquired_date ?? '';
  el('value_estimate').value = item.value_estimate ?? '';
  el('notes').value = item.notes || '';
  el('saveBtn').textContent = 'Update Item';
}

function resetForm(){
  el('itemForm').reset();
  el('id').value = '';
  el('saveBtn').textContent = 'Save Item';
}

function currentQuery(){
  const params = new URLSearchParams();
  const q = el('q').value.trim();
  const type = el('filterType').value;
  const country = el('filterCountry').value.trim();
  const year = el('filterYear').value.trim();
  if (q) params.set('q', q);
  if (type) params.set('type', type);
  if (country) params.set('country', country);
  if (year) params.set('year', year);
  return params.toString();
}

async function loadItems(){
  const qs = currentQuery();
  const url = qs ? `${API_ITEMS}?${qs}` : API_ITEMS;
  const res = await apiFetch(url, { method: 'GET' });
  const data = await res.json();
  renderItems(data.items || []);
}

function renderItems(items){
  const tbody = el('itemsTable').querySelector('tbody');
  tbody.innerHTML = '';
  for (const item of items){
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${esc(item.type)}</td>
      <td>${esc(item.name)}</td>
      <td>${esc(item.country)}</td>
      <td>${esc(item.year ?? '')}</td>
      <td>${esc(item.denomination)}</td>
      <td>${esc(item.condition)}</td>
      <td>${esc(item.value_estimate ?? '')}</td>
      <td>
        <button data-action="edit" data-id="${esc(item.id)}">Edit</button>
        <button data-action="delete" data-id="${esc(item.id)}">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  }
  el('countPill').textContent = `${items.length} item${items.length===1?'':'s'}`;

  tbody.querySelectorAll('button').forEach(btn=>{
    btn.addEventListener('click', async () => {
      const action = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');
      if (action === 'edit'){
        const item = items.find(x=>x.id===id);
        if (item) fillForm(item);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
      if (action === 'delete'){
        if (!confirm('Delete this item?')) return;
        const res = await apiFetch(API_ITEMS, { method: 'DELETE', body: JSON.stringify({ id }) });
        const out = await res.json();
        if (!out.ok) alert(out.message || 'Delete failed');
        await loadItems();
      }
    })
  });
}

async function saveItem(e){
  e.preventDefault();
  const payload = formToPayload();
  if (!payload.name){
    alert('Name/Title is required');
    return;
  }
  const isUpdate = Boolean(payload.id);
  const method = isUpdate ? 'PUT' : 'POST';
  const res = await apiFetch(API_ITEMS, { method, body: JSON.stringify(payload) });
  const out = await res.json();
  if (!out.ok){
    alert(out.message || 'Save failed');
    return;
  }
  resetForm();
  await loadItems();
}

function showHeaders(res){
  const lines = [];
  lines.push(`Status: ${res.status} ${res.statusText}`);
  const interesting = ['content-type','content-length','last-modified','cache-control','etag','server'];
  for (const [k,v] of res.headers.entries()){
    if (interesting.includes(k.toLowerCase())) lines.push(`${k}: ${v}`);
  }
  lines.push('');
  lines.push('Note: For HEAD, the response body is intentionally empty.');
  el('headResult').textContent = lines.join('\n');
}

async function headItems(){
  const qs = currentQuery();
  const url = qs ? `${API_ITEMS}?${qs}` : API_ITEMS;
  const res = await apiFetch(url, { method: 'HEAD' });
  showHeaders(res);
}

async function getItemsDemo(){
  const qs = currentQuery();
  const url = qs ? `${API_ITEMS}?${qs}` : API_ITEMS;
  const res = await apiFetch(url, { method: 'GET' });
  showHeaders(res);
  const txt = await res.text();
  el('headResult').textContent += `\n\nBody preview (GET):\n${txt.slice(0, 300)}${txt.length>300?'...':''}`;
}

async function headExport(){
  const res = await apiFetch(API_EXPORT, { method: 'HEAD' });
  showHeaders(res);
}

async function getExport(){
  // open in a new tab to show download; also show headers via fetch
  const res = await apiFetch(API_EXPORT, { method: 'GET' });
  showHeaders(res);
  const blob = await res.blob();
  const url = URL.createObjectURL(blob);
  window.open(url, '_blank');
}

el('itemForm').addEventListener('submit', saveItem);
el('resetBtn').addEventListener('click', resetForm);
el('searchBtn').addEventListener('click', loadItems);
el('clearFiltersBtn').addEventListener('click', () => {
  el('q').value=''; el('filterType').value=''; el('filterCountry').value=''; el('filterYear').value='';
  loadItems();
});

el('headItemsBtn').addEventListener('click', headItems);
el('getItemsBtn').addEventListener('click', getItemsDemo);
el('headExportBtn').addEventListener('click', headExport);
el('getExportBtn').addEventListener('click', getExport);

loadItems();
