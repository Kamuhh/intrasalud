(function () {
  const once = (fn) => { let done = false; return function(){ if(!done){ done=true; fn.apply(this, arguments);} }; };

  function normalizeBase(url){ if(!url) return ''; return url.endsWith('/') ? url : url + '/'; }

  const ADMIN  = normalizeBase(window.kc_admin_url || (window.ajaxurl ? window.ajaxurl.replace('admin-ajax.php','') : '/wp-admin/'));
  const ASSETS = normalizeBase(window.kc_assets_url || (window.kc_plugin_url ? (window.kc_plugin_url + 'assets/') : ''));
  const PLUGIN = normalizeBase(window.kc_plugin_url || '');

  // 1) Reparar <img> con src roto tipo /wp-admin/undefinedassets/...
  const fixBrokenImgSrc = () => {
    const badNeedle = '/wp-admin/undefinedassets/';
    document.querySelectorAll('img').forEach(img => {
      try {
        const src = img.getAttribute('src') || '';
        if (src.includes(badNeedle)) {
          const idx = src.indexOf('assets/');
          const tail = idx > -1 ? src.substring(idx) : '';
          if (tail) img.src = ASSETS + tail.replace(/^assets\//, '');
        }
      } catch(e){}
    });

      };

  // 2) Ocultar ítems por texto en el lateral del SPA
  const hideLeftNavItems = () => {
    const side = document.querySelector('[class*="sidebar"], nav, .kc-sidebar, .kc_left_sidebar') || document;
    const labelsToHide = ['Solicitar funciones', 'Obtener ayuda'];
    side.querySelectorAll('a, li, div, span').forEach(node => {
      const t = (node.textContent || '').trim();
      if (labelsToHide.includes(t)) {
        const item = node.closest('li, a, div') || node;
        if (item && item.style) item.style.display = 'none';
      }
    });
  };

  const init = once(function () {
    requestAnimationFrame(() => {
      fixBrokenImgSrc();
      hideLeftNavItems();
      const mo = new MutationObserver(() => {
        fixBrokenImgSrc();
        hideLeftNavItems();
      });
      mo.observe(document.documentElement, { childList: true, subtree: true });
    });
  });

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
  }
  setTimeout(init, 2500);
})();
