/* kc_patches.js — seguro y aislado */
(function () {
  try {
    // Bases con fallback
    var adminBase  = (window.kc_admin_url)  || (window.ajaxurl ? window.ajaxurl.replace('admin-ajax.php','') : '/wp-admin/');
    var assetsBase = (window.kc_assets_url) || (window.kc_plugin_url ? (window.kc_plugin_url + 'assets/') : '');
    function nbase(u){ if(!u) return ''; return u.endsWith('/') ? u : u + '/'; }
    adminBase  = nbase(adminBase);
    assetsBase = nbase(assetsBase);
    var BAD_NEEDLE = '/wp-admin/undefinedassets/';

    function fixBrokenImgSrc(root) {
      var scope = root || document;
      var list = scope.querySelectorAll('img[src]');
      for (var i=0;i<list.length;i++) {
        var img = list[i];
        var src = img.getAttribute('src') || '';
        if (src.indexOf(BAD_NEEDLE) !== -1) {
          var idx  = src.indexOf('assets/');
          var tail = idx > -1 ? src.substring(idx).replace(/^assets\//,'') : '';
          if (tail) img.src = assetsBase + tail;
        }
      }
    }

    // también corrige si falla la carga
    window.addEventListener('error', function (ev) {
      var t = ev && ev.target;
      if (t && t.tagName === 'IMG') {
        var src = t.getAttribute('src') || '';
        if (src.indexOf(BAD_NEEDLE) !== -1) {
          var idx  = src.indexOf('assets/');
          var tail = idx > -1 ? src.substring(idx).replace(/^assets\//,'') : '';
          if (tail) t.src = assetsBase + tail;
        }
      }
    }, true);

    function hideLeftNavItems(){
      var side = document.querySelector('[class*="sidebar"], nav, .kc-sidebar, .kc_left_sidebar') || document;
      var labels = ['Solicitar funciones','Obtener ayuda'];
      var nodes = side.querySelectorAll('a, li, div, span');
      for (var i=0;i<nodes.length;i++){
        var node = nodes[i];
        var txt = (node.textContent || '').trim();
        if (labels.indexOf(txt) !== -1) {
          var item = node.closest && node.closest('li, a, div') || node;
          if (item && item.style) item.style.display = 'none';
        }
      }
    }

    function inject(){
      fixBrokenImgSrc(document);
      hideLeftNavItems();
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', inject);
    } else {
      inject();
    }

    // Mantener el fix en cambios del SPA
    var mo = new MutationObserver(function(muts){
      for (var i=0;i<muts.length;i++){
        var m = muts[i];
        if (m.addedNodes && m.addedNodes.length) {
          for (var j=0;j<m.addedNodes.length;j++){
            var n = m.addedNodes[j];
            if (n && n.nodeType === 1) {
              fixBrokenImgSrc(n);
              hideLeftNavItems();
            }
          }
        }
      }
    });
    mo.observe(document.documentElement, {childList:true, subtree:true});
  } catch (e) {
    // Nunca romper el SPA por este parche
    console.error('kc_patches.js error', e);
  }
})();
