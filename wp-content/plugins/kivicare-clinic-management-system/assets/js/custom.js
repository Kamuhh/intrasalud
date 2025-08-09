
$ = jQuery;


function displayMessage(message, duration = 10000) {
    window.Snackbar.show({ text: escapeHtml(message), pos: 'top-right', duration: duration, actionText: window.__kivicarelang.common.dismiss });
}

function displayErrorMessage(message, duration = 10000) {
    window.Snackbar.show({ text: escapeHtml(message), pos: 'top-right', backgroundColor: '#f5365c', actionTextColor: '#fff', duration: duration });
}
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}



function displayAlert(title, message, color = 'red') {
    $.alert({
        title: title,
        content: message,
        type: color,
    });
}

function displayTooltip(object = {}) {
    setTimeout(() => {
        let classElement = object.class !== undefined ? object.class : '.guide';
        window.Tipped.create(classElement, function (element) {
            return {
                content: $(element).data('content')
            };
        }, {
            position: object.position !== undefined ? object.position : 'right',
            skin: object.skin !== undefined ? object.skin : 'light',
            size: object.size !== undefined ? object.size : 'large'
        });
    }, 1000);
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

window.onload = function () {
    $('.fc-toolbar.fc-header-toolbar').addClass('row col-lg-12');
};

function kiviOpenPaymentWindow(url) {

    const parsedUrl = new URL(url);
    let hostname = parsedUrl.hostname;
    hostname = hostname.replace(/^www\./, '');
    const parts = hostname.split('.');
    const domainName = parts.slice(-2).join('.');
    const currentHost = window.location.hostname;

    if (domainName === "paypal.com" || hostname === currentHost) {
        // Domain matches, open the payment window
        return window.open(
            url,
            '_blank',
            'popup=yes,toolbar=0,status=0,width=360,height=500,top=100,left=' +
            (window.screen ? Math.round(screen.width / 2 - 275) : 100)
        );
    } else {
        console.error('Unauthorized domain:', domainName);
        return null;
    }

}

function kivicare_generate_time(n, a = "delimiters", c = "general") {
    var o = n.startDate.split("-"),
        i = n.endDate.split("-");
    let l = "",
        r = "",
        s = !1;
    if (null != n.startTime && null != n.endTime)
        if (null != n.timeZoneOffset && "" != n.timeZoneOffset)
            (l = new Date(o[0] + "-" + o[1] + "-" + o[2] + "T" + n.startTime + ":00.000" + n.timeZoneOffset)),
                (r = new Date(i[0] + "-" + i[1] + "-" + i[2] + "T" + n.endTime + ":00.000" + n.timeZoneOffset)),
                (l = l.toISOString().replace(".000", "")),
                (r = r.toISOString().replace(".000", "")),
                "clean" == a && ((l = l.replace(/\-/g, "").replace(/\:/g, "")), (r = r.replace(/\-/g, "").replace(/\:/g, "")));
        else {
            if (((l = new Date(o[0] + "-" + o[1] + "-" + o[2] + "T" + n.startTime + ":00.000+00:00")), (r = new Date(i[0] + "-" + i[1] + "-" + i[2] + "T" + n.endTime + ":00.000+00:00")), null != n.timeZone && "" != n.timeZone)) {
                let e = new Date(l.toLocaleString("en-US", { timeZone: "UTC" })),
                    t = ("currentBrowser" == n.timeZone && (n.timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone), new Date(l.toLocaleString("en-US", { timeZone: n.timeZone })));
                n = e.getTime() - t.getTime();
                l.setTime(l.getTime() + n), r.setTime(r.getTime() + n);
            }
            (l = l.toISOString().replace(".000", "")), (r = r.toISOString().replace(".000", "")), "clean" == a && ((l = l.replace(/\-/g, "").replace(/\:/g, "")), (r = r.replace(/\-/g, "").replace(/\:/g, "")));
        }
    else {
        (s = !0), (l = new Date(Date.UTC(o[0], o[1] - 1, o[2])));
        let e = l.toISOString().replace(/T(.+)Z/g, ""),
            t = ((r = new Date(Date.UTC(i[0], i[1] - 1, i[2]))), ("google" != c && "microsoft" != c && "ical" != c) || r.setDate(r.getDate() + 1), r.toISOString().replace(/T(.+)Z/g, ""));
        "clean" == a && ((e = e.replace(/\-/g, "")), (t = t.replace(/\-/g, ""))), (l = e), (r = t);
    }
    return { start: l, end: r, allday: s };
}


function kivicare_generate_google(e) {
    try {
        let t = "https://calendar.google.com/calendar/render?action=TEMPLATE";
        var n = kivicare_generate_time(e, "clean", "google");
        if (!n) {
            throw new Error("Failed to generate time for Google calendar.");
        }
        t += "&dates=" + n.start + "%2F" + n.end;
        if (e.name && e.name !== "") {
            t += "&text=" + encodeURIComponent(e.name);
        }
        if (e.location && e.location !== "") {
            t += "&location=" + encodeURIComponent(e.location);
            if (kivicare_isiOS()) {
                e.description = e.description || "";
                e.description += "<br><br>&#128205;: " + e.location;
            }
        }
        if (e.description && e.description !== "") {
            t += "&details=" + encodeURIComponent(e.description);
        }
        window.open(t, "_blank").focus();
        console.log(t);
    } catch (error) {
        console.error("Error generating Google calendar link:", error);
    }
}

function kivicare_generate_yahoo(e) {

    try {
        let t = "https://calendar.yahoo.com/?v=60";
        var n = kivicare_generate_time(e, "clean");
        if (!n) {
            throw new Error("Failed to generate time for Yahoo calendar.");
        }
        t += "&st=" + n.start + "&et=" + n.end;
        if (n.allday) {
            t += "&dur=allday";
        }
        if (e.name && e.name !== "") {
            t += "&title=" + encodeURIComponent(e.name);
        }
        if (e.location && e.location !== "") {
            t += "&in_loc=" + encodeURIComponent(e.location);
        }
        if (e.description && e.description !== "") {
            t += "&desc=" + encodeURIComponent(e.description);
        }
        window.open(t, "_blank").focus();
        console.log(t);
    } catch (error) {
        console.error("Error generating Yahoo calendar link:", error);
    }

}

function kivicare_generate_microsoft(e, t = "365") {


    try {
        let n = "https://";
        n += e.provider === "outlook" ? "outlook.live.com" : "outlook.office.com";
        n += "/calendar/0/deeplink/compose?path=%2Fcalendar%2Faction%2Fcompose&rru=addevent";

        let t = kivicare_generate_time(e, "delimiters", "microsoft");
        if (!t) {
            throw new Error("Failed to generate time for Microsoft Outlook calendar.");
        }
        n += "&startdt=" + t.start + "&enddt=" + t.end;
        if (t.allday) {
            n += "&allday=true";
        }
        if (e.name && e.name !== "") {
            n += "&subject=" + encodeURIComponent(e.name);
        }
        if (e.location && e.location !== "") {
            n += "&location=" + encodeURIComponent(e.location);
        }
        if (e.description && e.description !== "") {
            n += "&body=" + encodeURIComponent(e.description.replace(/\n/g, "<br>"));
        }
        window.open(n, "_blank").focus();
        console.log(n);
    } catch (error) {
        console.error("Error generating Microsoft Outlook calendar link:", error);
    }
}

function kivicare_generate_teams(e) {
    let t = "https://teams.microsoft.com/l/meeting/new?";
    var n = kivicare_generate_time(e, "delimiters", "microsoft");
    t += "&startTime=" + n.start + "&endTime=" + n.end;
    let a = "";
    null != e.name && "" != e.name && (t += "&subject=" + encodeURIComponent(e.name)),
        null != e.location && "" != e.location && ((a = encodeURIComponent(e.location)), (t += "&location=" + a), (a += " // ")),
        null != e.description && "" != e.description && (t += "&content=" + a + encodeURIComponent(e.description)),
        // window.open(t, "_blank").focus();
        console.log(t)
}

function kivicare_generate_ical(t) {
    let e = new Date();
    e = e
        .toISOString()
        .replace(/\..../g, "")
        .replace(/[^a-z0-9]/gi, "");
    var n = kivicare_generate_time(t, "clean", "ical");
    let a = "",
        c = (n.allday && (a = ";VALUE=DATE"), ["BEGIN:VCALENDAR", "VERSION:2.0", "CALSCALE:GREGORIAN", "BEGIN:VEVENT", "DTSTAMP:" + n.start, "DTSTART" + a + ":" + n.start, "DTEND" + a + ":" + n.end, "SUMMARY:" + t.name]);
    null != t.description_iCal && "" != t.description_iCal && c.push("DESCRIPTION:" + t.description_iCal.replace(/\n/g, "\\n")),
        null != t.location && "" != t.location && c.push("LOCATION:" + t.location),
        c.push("STATUS:CONFIRMED", "LAST-MODIFIED:" + e, "SEQUENCE:0", "END:VEVENT", "END:VCALENDAR");
    n = "data:text/calendar;charset=utf-8," + encodeURIComponent(c.join("\r\n"));
    try {
        if (!window.ActiveXObject) {
            let e = document.createElement("a");
            (e.href = n), (e.target = "_blank"), (e.download = t.iCalFileName || "event-to-save-in-my-calendar");
            var o = new MouseEvent("click", { view: window, bubbles: !0, cancelable: !1 });
            e.dispatchEvent(o), (window.URL || window.webkitURL).revokeObjectURL(e.href);
        }
    } catch (e) {
        console.error(e);
    }
}


function kivicare_isiOS() {
    if (!kivicare_isBrowser()) {
        return false;
    }

    // Directly check the conditions without using new Function
    return (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) ||
        (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
}

function kivicare_isBrowser() {
    try { return this === window; } catch (e) { return false; }
}

function kivicare_add_to_calendar_url(config, type) {
    switch (type) {
        case 'googleCalender':
            kivicare_generate_google(config);
            break;
        case 'microSoftOutlookLive':
            kivicare_generate_microsoft(config, 'outlook')
            break;
        case 'microSoftOutlookoffice':
            kivicare_generate_microsoft(config)
            break;
        case 'microSoftTeam':
            kivicare_generate_teams(config)
            break;
        case 'yahoo':
            kivicare_generate_yahoo(config);
            break;
        case 'apple':
            kivicare_generate_ical(config)
            break;
    }
}
function kivicareCustomImageUploader(formTranslation, type = '', multiple = false, extraData = {}) {
    let options = {
        title: ['report', 'csv', 'xls', 'json', 'custom_field'].includes(type) ? formTranslation.common.choose_file : formTranslation.common.choose_image,
        button: {
            text: ['report', 'csv', 'xls', 'json', 'custom_field'].includes(type) ? formTranslation.common.choose_file : formTranslation.common.choose_image
        },
        library: {
            type: ['image']
        },
        multiple: multiple
    }

    if (type === 'report') {
        options.library.type = Object.values(kc_custom_request_data.support_mime_type);
    } else if (type === 'csv') {
        options.library.type = ['text/csv']
    } else if (type === 'xls') {
        options.library.type = ['application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel.sheet.macroEnabled.12', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12']
    } else if (type === 'json') {
        options.library.type = ['application/json']
    } else if (type === 'custom_field') {
        delete options.library;
        options.library = {
            type :extraData.mediaType
        }
    }

    const wp_media_instance = wp.media.frames.file_frame = wp.media(options);

    if(options.library && options.library.type && options.library.type.length > 0){
        wp_media_instance.on('uploader:ready',function (){
            jQuery( '.moxie-shim-html5 input[type="file"]' ).attr('accept',options.library.type.join(','))
        });
    }

    return  wp_media_instance;
}
/* kc_dashboard_extras: parche no intrusivo para SPA (imgs rotas + ocultar 2 ítems) */
(function () {
  const once = (fn) => { let done=false; return function(){ if(!done){ done=true; try{ fn.apply(this, arguments);}catch(e){} } }; };
  const nbase = (u)=>{ if(!u) return ''; return u.endsWith('/')?u:(u+'/'); };

  // Bases seguras: vienen de PHP (ver sección C). Fallbacks defensivos.
  const ADMIN  = nbase(window.kc_admin_url || (window.ajaxurl ? window.ajaxurl.replace('admin-ajax.php','') : '/wp-admin/'));
  const ASSETS = nbase(window.kc_assets_url || (window.kc_plugin_url ? (window.kc_plugin_url + 'assets/') : ''));
  const BAD_NEEDLE = '/wp-admin/undefinedassets/';

  // Reparar <img> que el bundle armó con base 'undefined'
  function fixBrokenImgSrc() {
    document.querySelectorAll('img[src]').forEach(img => {
      const src = img.getAttribute('src') || '';
      if (src.includes(BAD_NEEDLE)) {
        const idx = src.indexOf('assets/');
        const tail = idx > -1 ? src.substring(idx).replace(/^assets\//,'') : '';
        if (tail) img.src = ASSETS + tail;
      }
    });
  }

  // fallback por error de carga
  window.addEventListener('error', function(ev){
    const t = ev && ev.target;
    if (t && t.tagName === 'IMG') {
      const src = t.getAttribute('src') || '';
      if (src.includes(BAD_NEEDLE)) {
        const idx = src.indexOf('assets/');
        const tail = idx > -1 ? src.substring(idx).replace(/^assets\//,'') : '';
        if (tail) t.src = ASSETS + tail;
      }
    }
  }, true);

  // Ocultar “Solicitar funciones” y “Obtener ayuda” en el lateral del SPA
  function hideLeftNavItems() {
    const labels = ['Solicitar funciones','Obtener ayuda'];
    const root = document.querySelector('[class*="sidebar"], nav, .kc-sidebar, .kc_left_sidebar') || document;
    root.querySelectorAll('a, li, div, span').forEach(node => {
      const t = (node.textContent || '').trim();
      if (labels.includes(t)) {
        const item = node.closest('li, a, div') || node;
        if (item && item.style) item.style.display = 'none';
      }
    });
  }

  const init = once(function(){
    // No bloquear el SPA; ejecutar cuando ya montó algo de UI
    requestAnimationFrame(() => {
      fixBrokenImgSrc();
      hideLeftNavItems();
      // Mantener el fix en cambios de ruta/vista
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
  setTimeout(init, 2500); // salvaguarda si el SPA tarda
})();

/* Resumen de consulta: botón y modal */
(function(){
  const once = (fn)=>{let d=false;return function(){if(!d){d=true;fn.apply(this,arguments);}}};

  function openSummary(encounterId){
    // Usa la ruta ya registrada en KCRoutes: patient_encounter_summary
    // El plugin resuelve las rutas vía admin-ajax. Mantener el patrón de otras vistas.
    const url = ajaxData.ajaxurl + '?action=kivi_route&route=patient_encounter_summary&id=' + encodeURIComponent(encounterId) + '&TB_iframe=true&width=980&height=640';
    if (window.tb_show) {
      tb_show('Resumen de consulta', url);
    } else {
      window.open(url, '_blank');
    }
  }

  function injectButtons(){
    // Lista de encuentros: buscar la columna de acciones y añadir botón "Resumen"
    document.querySelectorAll('[data-encounter-id]').forEach(function(row){
      // evitar duplicados
      if (row.querySelector('.kc-btn-summary')) return;
      const id = row.getAttribute('data-encounter-id');
      const actions = row.querySelector('.actions, [data-col="actions"], td:last-child');
      if (!actions) return;
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'button button-small kc-btn-summary';
      btn.textContent = (window.__kivicarelang && __kivicarelang.common && __kivicarelang.common.summary) || 'Resumen';
      btn.addEventListener('click', function(){ openSummary(id); });
      actions.appendChild(btn);
    });

    // Detalle del encuentro: si hay un contenedor con el ID del encuentro, añade botón flotante
    const detail = document.querySelector('[data-encounter-detail-id]');
    if (detail && !document.querySelector('.kc-fab-summary')) {
      const id = detail.getAttribute('data-encounter-detail-id');
      const fab = document.createElement('button');
      fab.type = 'button';
      fab.className = 'button button-primary kc-fab-summary';
      fab.style.position='fixed'; fab.style.right='16px'; fab.style.bottom='16px'; fab.style.zIndex='9999';
      fab.textContent = 'Resumen';
      fab.addEventListener('click', function(){ openSummary(id); });
      document.body.appendChild(fab);
    }
  }

  const init = once(function(){
    injectButtons();
    // Mantener al cambiar de ruta en el SPA
    const mo = new MutationObserver(injectButtons);
    mo.observe(document.documentElement, {childList:true,subtree:true});
  });

  if (document.readyState==='loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
