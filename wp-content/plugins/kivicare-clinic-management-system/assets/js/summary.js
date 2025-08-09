/* Inyecta botón "Resumen" en lista y detalle sin romper el SPA */
(function(){
  const once = (fn)=>{let d=false;return function(){if(!d){d=true;fn();}}};

  function openSummary(encounterId){
    const url = ajaxData.ajaxurl + '?action=kivi_route&route=patient_encounter_summary&id=' + encodeURIComponent(encounterId) + '&TB_iframe=true&width=980&height=640';
    if (window.tb_show) tb_show('Resumen de consulta', url);
    else window.open(url,'_blank');
  }

  function inject(){
    // En filas con data-encounter-id
    document.querySelectorAll('[data-encounter-id]').forEach(row=>{
      if (row.querySelector('.kc-btn-summary')) return;
      const id = row.getAttribute('data-encounter-id');
      const actions = row.querySelector('.actions, [data-col="actions"], td:last-child');
      if (!actions || !id) return;
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'button button-small kc-btn-summary';
      btn.textContent = 'Resumen';
      btn.addEventListener('click', ()=>openSummary(id));
      actions.appendChild(btn);
    });

    // En detalle con data-encounter-detail-id
    const detail = document.querySelector('[data-encounter-detail-id]');
    if (detail && !document.querySelector('.kc-fab-summary')) {
      const id = detail.getAttribute('data-encounter-detail-id');
      const fab = document.createElement('button');
      fab.type = 'button';
      fab.className = 'kc-fab-summary';
      Object.assign(fab.style,{position:'fixed',right:'16px',bottom:'16px',zIndex:'9999',padding:'10px 14px',borderRadius:'10px',border:'1px solid #e5e7eb',background:'#111827',color:'#fff',cursor:'pointer'});
      fab.textContent = 'Resumen';
      fab.addEventListener('click', ()=>openSummary(id));
      document.body.appendChild(fab);
    }
  }

  const init = once(function(){
    inject();
    const mo = new MutationObserver(inject);
    mo.observe(document.documentElement,{childList:true,subtree:true});
  });

  if (document.readyState==='loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
