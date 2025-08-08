
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
            type: extraData.mediaType
        }
    }

    const wp_media_instance = wp.media.frames.file_frame = wp.media(options);

    if (options.library && options.library.type && options.library.type.length > 0) {
        wp_media_instance.on('uploader:ready', function () {
            jQuery('.moxie-shim-html5 input[type="file"]').attr('accept', options.library.type.join(','))
        });
    }

    return wp_media_instance;
}


function kc_ajax_get(action, id) {
    return jQuery.get(
        ajaxData.ajaxurl,
        { action: action, id: id },
        function (response) {
            console.log(response);
        }
    );
}

// helper: extrae ID del hash (#/patient-encounter/dashboard/17?...)
function idFromHash() {
  const h = String(location.hash || "");
  // captura 17 tanto en /patient-encounter/17 como en /patient-encounter/dashboard/17
  let m =
    h.match(/patient-encounter\/(?:dashboard\/)?(\d+)/) ||
    h.match(/patient-encounter\/(\d+)/) ||
    h.match(/\/(\d+)(?:[/?]|$)/); // súper fallback: último número en la ruta
  return m ? m[1] : null;
}

// Expose encounter_id in admin so custom.js can read it
document.addEventListener('DOMContentLoaded', function () {
  function attachEncounterId() {
    var id = null;
    if (window.KCApp) {
      if (
        window.KCApp.$store &&
        window.KCApp.$store.state &&
        window.KCApp.$store.state.encounter &&
        window.KCApp.$store.state.encounter.id
      ) {
        id = window.KCApp.$store.state.encounter.id;
      } else if (
        window.KCApp.$route &&
        window.KCApp.$route.params &&
        window.KCApp.$route.params.id
      ) {
        id = window.KCApp.$route.params.id;
      }
    }

    if (!id) id = idFromHash();

    if (id) {
      window.kcEncounterId = id;
      var btn = document.getElementById('kc-encounter-print');
      if (btn) btn.setAttribute('data-encounter-id', id);
      console.log('[Resumen] kcEncounterId set', id);
      return true;
    }
    return false;
  }

  if (!attachEncounterId()) {
    var tries = 0;
    var timer = setInterval(function () {
      tries++;
      if (attachEncounterId() || tries > 20) {
        clearInterval(timer);
      }
    }, 500);
  }
});

// ENGANCHE BACKEND - Resumen de atención
(function() {
  function normalize(t){ return (t||'').trim().toLowerCase().replace(/\s+/g,' '); }

  function matchResumenButton(el){
    if(!el) return null;
    const btn = el.closest('#kc-encounter-print');
    if (btn) return btn;
    // Fallback por texto
    const maybeBtn = el.closest('button, a, [role="button"]');
    if (maybeBtn && normalize(maybeBtn.textContent) === 'resumen de atención') return maybeBtn;
    return null;
  }

  function resolveEncounterId(btn){
    let id = btn && btn.getAttribute('data-encounter-id');
    if (id) return id;
    if (window.kcEncounterId) return window.kcEncounterId;
    const params = new URLSearchParams(location.search || '');
    id = params.get('encounter_id') || params.get('id');
    if (id) return id;
    id = idFromHash();
    if (id) return id;
    return null;
  }

  document.addEventListener('click', function(e){
    const btn = matchResumenButton(e.target);
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    console.log('[Resumen] click intercepted', btn);
    btn.style.outline = '2px solid #28a745'; setTimeout(()=>btn.style.outline='', 1000);

    let encounterId = resolveEncounterId(btn);
    if (!encounterId) {
      console.warn('[Resumen] No encounter_id');
      return;
    }

    jQuery.post(request_data.ajaxurl, {
      action: 'ajax_get',
      route_name: 'patient_encounter_summary',
      encounter_id: encounterId,
      type: 'html',
      _ajax_nonce: request_data.get_nonce
    }).done(function(res){
      try { if (typeof res === 'string') res = JSON.parse(res); } catch(err){ console.error('[Resumen] Invalid response', res); return; }
      if (!res || !res.status) { console.error('[Resumen] Error', res); return; }

      let $modal = jQuery('#encounter-summary-modal');
      if (!$modal.length) {
        $modal = jQuery('<div id="encounter-summary-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">\
<div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">\
<div class="modal-header"><h5 class="modal-title">Resumen de atención</h5>\
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>\
<div class="modal-body" id="encounter-summary-content"></div>\
<div class="modal-footer">\
<button type="button" class="btn btn-primary" id="encounter-summary-email">Correo electrónico</button>\
<button type="button" class="btn btn-primary" id="encounter-summary-pdf">PDF</button>\
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>\
</div></div></div></div>');
        jQuery('body').append($modal);
      }

      jQuery('#encounter-summary-content').html(res.data);
      const modal = new bootstrap.Modal($modal[0]); modal.show();

      jQuery('#encounter-summary-email').off('click').on('click', function(){
        jQuery.post(request_data.ajaxurl, {
            action: 'ajax_get',
            route_name: 'patient_encounter_summary',
            encounter_id: encounterId,
            type: 'sendEmail',
            _ajax_nonce: request_data.get_nonce
          })
          .done(function(r){ alert(r.message); })
          .fail(function(jqXHR){
            console.error('[Resumen] AJAX error', jqXHR.status, jqXHR.responseText);
          });
      });
      jQuery('#encounter-summary-pdf').off('click').on('click', function(){
        jQuery.post(request_data.ajaxurl, {
            action: 'ajax_get',
            route_name: 'patient_encounter_summary',
            encounter_id: encounterId,
            type: 'pdf',
            _ajax_nonce: request_data.get_nonce
          })
          .done(function(r){ if (r.file_url) window.open(r.file_url, '_blank'); })
          .fail(function(jqXHR){
            console.error('[Resumen] AJAX error', jqXHR.status, jqXHR.responseText);
          });
      });
    }).fail(function(jqXHR){
      console.error('[Resumen] AJAX error', jqXHR.status, jqXHR.responseText);
    });
  }, true);

  console.log('[Resumen] handler attached (backend)');
})();
