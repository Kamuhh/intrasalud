<?php defined('ABSPATH') || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?> >
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php esc_html_e('Resumen de consulta','kc-lang'); ?></title>
<style>
  :root{--b:#111827;--m:#6b7280;--bd:#e5e7eb;--bg:#fff;--chip:#eef2ff}
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;margin:0;background:#f8fafc;color:var(--b)}
  .wrap{max-width:1100px;margin:16px auto;padding:0 16px}
  .card{background:var(--bg);border:1px solid var(--bd);border-radius:12px;padding:16px;margin-bottom:16px}
  .row{display:flex;flex-wrap:wrap;gap:16px}
  .col{flex:1 1 260px}
  h2{margin:0 0 8px 0;font-size:20px}
  h3{margin:0 0 8px 0;font-size:16px}
  .muted{color:var(--m)}
  .table{width:100%;border-collapse:collapse}
  .table th,.table td{border-bottom:1px solid var(--bd);padding:8px 12px;text-align:left;vertical-align:top}
  .right{text-align:right}
  .chip{display:inline-block;padding:2px 8px;border-radius:999px;background:var(--chip)}
  @media print {.no-print{display:none!important} body{background:#fff}}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h2><?php esc_html_e('Resumen de consulta','kc-lang'); ?></h2>
    <div class="muted" id="printDate"></div>
  </div>

  <div class="row">
    <div class="col">
      <div class="card">
        <h3><?php esc_html_e('Paciente','kc-lang'); ?></h3>
        <div id="p_name"></div>
        <div class="muted" id="p_email"></div>
        <div class="muted" id="p_phone"></div>
      </div>
    </div>
    <div class="col">
      <div class="card">
        <h3><?php esc_html_e('Doctor','kc-lang'); ?></h3>
        <div id="d_name"></div>
        <div class="muted" id="d_spec"></div>
        <div class="muted" id="c_name"></div>
      </div>
    </div>
    <div class="col">
      <div class="card">
        <h3><?php esc_html_e('Consulta','kc-lang'); ?></h3>
        <div><?php esc_html_e('ID','kc-lang'); ?>: <span id="e_id"></span></div>
        <div><?php esc_html_e('Fecha','kc-lang'); ?>: <span id="e_date"></span></div>
        <div><?php esc_html_e('Estado','kc-lang'); ?>: <span class="chip" id="e_status"></span></div>
      </div>
    </div>
  </div>

  <div class="card">
    <h3><?php esc_html_e('Signos vitales','kc-lang'); ?></h3>
    <table class="table" id="vitals"><tbody>
      <tr><td class="muted"><?php esc_html_e('Cargando...','kc-lang'); ?></td></tr>
    </tbody></table>
  </div>

  <div class="row">
    <div class="col">
      <div class="card">
        <h3><?php esc_html_e('Diagnóstico','kc-lang'); ?></h3>
        <div id="diagnosis" class="muted"><?php esc_html_e('Sin datos','kc-lang'); ?></div>
      </div>
    </div>
    <div class="col">
      <div class="card">
        <h3><?php esc_html_e('Notas','kc-lang'); ?></h3>
        <div id="notes" class="muted"><?php esc_html_e('Sin notas','kc-lang'); ?></div>
      </div>
    </div>
  </div>

  <div class="card">
    <h3><?php esc_html_e('Prescripción','kc-lang'); ?></h3>
    <table class="table" id="rx">
      <thead>
        <tr>
          <th><?php esc_html_e('Medicamento','kc-lang'); ?></th>
          <th><?php esc_html_e('Dosis','kc-lang'); ?></th>
          <th><?php esc_html_e('Frecuencia','kc-lang'); ?></th>
          <th><?php esc_html_e('Duración','kc-lang'); ?></th>
          <th><?php esc_html_e('Notas','kc-lang'); ?></th>
        </tr>
      </thead>
      <tbody><tr><td class="muted" colspan="5"><?php esc_html_e('Sin prescripción','kc-lang'); ?></td></tr></tbody>
    </table>
  </div>

  <div class="card">
    <h3><?php esc_html_e('Servicios asociados','kc-lang'); ?></h3>
    <table class="table" id="svcs">
      <thead>
        <tr>
          <th><?php esc_html_e('Servicio','kc-lang'); ?></th>
          <th class="right"><?php esc_html_e('Monto','kc-lang'); ?></th>
        </tr>
      </thead>
      <tbody><tr><td class="muted" colspan="2"><?php esc_html_e('Sin servicios','kc-lang'); ?></td></tr></tbody>
      <tfoot><tr><th><?php esc_html_e('Total','kc-lang'); ?></th><th class="right" id="svcs_total">0.00</th></tr></tfoot>
    </table>
  </div>

  <div class="card no-print" style="text-align:right">
    <button onclick="window.print()"><?php esc_html_e('Imprimir','kc-lang'); ?></button>
  </div>
</div>

<script>
(function(){
  const ajax = <?php echo json_encode($data['ajax_url']); ?>;
  const id   = <?php echo (int)$data['encounter_id']; ?>;
  const $    = (s)=>document.querySelector(s);
  const fmt  = (n)=>Number(n||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
  $('#printDate').textContent = new Date().toLocaleString();

  // Usar EXACTAMENTE el router del core (action=ajax_get).
  // La ruta patient_encounter_details tiene nonce deshabilitado (ver KCRoutes).
  const url = ajax
    + '?action=ajax_get'
    + '&route_name=patient_encounter_details'
    + '&encounter_id=' + encodeURIComponent(id)
    + '&type=json';

  fetch(url, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(j => {
      const d = j.data || j || {};
      const enc = d.encounter || d || {};
      const pat = d.patient || enc.patient || {};
      const doc = d.doctor || enc.doctor || {};
      const clinic = d.clinic || enc.clinic || {};

      $('#e_id').textContent    = enc.id || id;
      $('#e_date').textContent  = enc.date || enc.encounter_date || '';
      $('#e_status').textContent= enc.status || enc.encounter_status || '';

      $('#p_name').textContent  = pat.name || pat.full_name || '';
      $('#p_email').textContent = pat.email || '';
      $('#p_phone').textContent = pat.phone_no || pat.phone || '';

      $('#d_name').textContent  = doc.name || '';
      $('#d_spec').textContent  = doc.speciality || doc.specialty || '';
      $('#c_name').textContent  = clinic.name || '';

      // Vitals
      const vt = d.vitals || enc.vitals || {};
      const vtBody = document.querySelector('#vitals tbody'); vtBody.innerHTML = '';
      const entries = Object.entries(vt);
      if (entries.length){
        entries.forEach(([k,v])=>{
          const tr = document.createElement('tr');
          tr.innerHTML = '<th>'+k+'</th><td>'+String(v??'')+'</td>';
          vtBody.appendChild(tr);
        });
      } else {
        vtBody.innerHTML = '<tr><td class="muted"><?php echo esc_html__('Sin registros','kc-lang'); ?></td></tr>';
      }

      // Diagnóstico / Notas
      const dx = d.diagnosis || enc.diagnosis || '';
      const nt = d.notes || enc.notes || '';
      if (dx) { const el = $('#diagnosis'); el.classList.remove('muted'); el.textContent = dx; }
      if (nt) { const el = $('#notes'); el.classList.remove('muted'); el.textContent = nt; }

      // Prescripción
      const rx = d.prescription || enc.prescription || d.prescriptions || [];
      const rxBody = document.querySelector('#rx tbody'); rxBody.innerHTML = '';
      if (Array.isArray(rx) && rx.length){
        rx.forEach(row=>{
          const tr = document.createElement('tr');
          tr.innerHTML =
            '<td>'+(row.medicine||row.name||'')+'</td>'+
            '<td>'+(row.dose||'')+'</td>'+
            '<td>'+(row.frequency||'')+'</td>'+
            '<td>'+(row.duration||'')+'</td>'+
            '<td>'+(row.note||row.notes||'')+'</td>';
          rxBody.appendChild(tr);
        });
      } else {
        rxBody.innerHTML = '<tr><td class="muted" colspan="5"><?php echo esc_html__('Sin prescripción','kc-lang'); ?></td></tr>';
      }

      // Servicios
      const sv = d.services || enc.services || [];
      const svBody = document.querySelector('#svcs tbody'); svBody.innerHTML = '';
      let total = 0;
      if (Array.isArray(sv) && sv.length){
        sv.forEach(s=>{
          const price = Number(s.price||s.amount||0);
          total += price;
          const tr = document.createElement('tr');
          tr.innerHTML = '<td>'+(s.name||'')+'</td><td class="right">'+fmt(price)+'</td>';
          svBody.appendChild(tr);
        });
      } else {
        svBody.innerHTML = '<tr><td class="muted" colspan="2"><?php echo esc_html__('Sin servicios','kc-lang'); ?></td></tr>';
      }
      document.querySelector('#svcs_total').textContent = fmt(total);
    })
    .catch(e => {
      console.error(e);
      alert('No se pudo cargar el resumen.');
    });
})();
</script>
</body>
</html>
