/**
 * Widget del asistente IA del portafolio.
 * Uso: <script src="/chatbot/widget.js" data-endpoint="/chat/messages" defer></script>
 * Requiere <meta name="csrf-token" content="..."> en la página.
 */
(function () {
  'use strict';

  var script = document.currentScript;
  var cfg = {
    endpoint: (script && script.dataset.endpoint) || '/chat/messages',
    title: (script && script.dataset.title) || 'Asistente virtual',
    greeting:
      (script && script.dataset.greeting) ||
      '¡Hola! Soy el asistente con IA del portafolio. ¿Qué proyecto tienes en mente? Te ayudo a elegir la mejor solución y a cotizarla.',
    suggestions: ['Quiero una página web', '¿Cuánto cuesta una tienda online?', 'Quiero un chatbot con IA', '¿Cómo es el proceso?'],
  };

  var STORAGE_KEY = 'portafolio_chat_v1';
  var state = load() || { conversationId: null, messages: [] };
  var sending = false;

  function load() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      return null;
    }
  }

  function save() {
    try {
      state.messages = state.messages.slice(-40);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    } catch (e) {
      /* almacenamiento no disponible: el chat sigue funcionando en memoria */
    }
  }

  function el(tag, cls, text) {
    var node = document.createElement(tag);
    if (cls) node.className = cls;
    if (text) node.textContent = text;
    return node;
  }

  // Convierte texto plano en nodos seguros: saltos de línea y enlaces clicables.
  function renderText(container, text) {
    var urlRe = /(https?:\/\/[^\s<]+[^\s<.,;:!?)\]])/g;
    text.split('\n').forEach(function (line, i) {
      if (i > 0) container.appendChild(document.createElement('br'));
      var last = 0;
      line.replace(urlRe, function (url, _m, offset) {
        if (offset > last) container.appendChild(document.createTextNode(line.slice(last, offset)));
        var a = el('a', null, url.replace(/^https?:\/\//, ''));
        a.href = url;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        container.appendChild(a);
        last = offset + url.length;
        return url;
      });
      if (last < line.length) container.appendChild(document.createTextNode(line.slice(last)));
    });
  }

  // ---------- Interfaz ----------
  var root = el('div', 'pcw');
  root.setAttribute('data-open', 'false');

  var launcher = el('button', 'pcw-launcher');
  launcher.type = 'button';
  launcher.setAttribute('aria-label', 'Abrir el asistente virtual');
  launcher.innerHTML =
    '<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path fill="currentColor" d="M12 3C6.5 3 2 6.9 2 11.7c0 2.6 1.3 4.9 3.4 6.5L4.6 22l4.1-2.2c1 .3 2.1.4 3.3.4 5.5 0 10-3.9 10-8.6S17.5 3 12 3Zm-4 9.9a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6Zm4 0a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6Zm4 0a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6Z"/></svg>';

  var panel = el('section', 'pcw-panel');
  panel.setAttribute('role', 'dialog');
  panel.setAttribute('aria-label', cfg.title);

  var header = el('header', 'pcw-header');
  var headText = el('div');
  headText.appendChild(el('strong', null, cfg.title));
  headText.appendChild(el('span', 'pcw-sub', 'Asistente con IA · responde al instante'));
  var close = el('button', 'pcw-close', '×');
  close.type = 'button';
  close.setAttribute('aria-label', 'Cerrar');
  header.appendChild(headText);
  header.appendChild(close);

  var list = el('div', 'pcw-messages');
  list.setAttribute('aria-live', 'polite');

  var chips = el('div', 'pcw-chips');

  var form = el('form', 'pcw-form');
  var input = el('textarea', 'pcw-input');
  input.rows = 1;
  input.maxLength = 1000;
  input.placeholder = 'Escribe tu mensaje…';
  input.setAttribute('aria-label', 'Mensaje');
  var send = el('button', 'pcw-send');
  send.type = 'submit';
  send.setAttribute('aria-label', 'Enviar');
  send.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M3.4 20.4 21 12 3.4 3.6 3.3 10l12.6 2-12.6 2z"/></svg>';
  form.appendChild(input);
  form.appendChild(send);

  var foot = el('p', 'pcw-foot', 'Las respuestas las genera una IA y pueden contener errores.');

  panel.appendChild(header);
  panel.appendChild(list);
  panel.appendChild(chips);
  panel.appendChild(form);
  panel.appendChild(foot);
  root.appendChild(panel);
  root.appendChild(launcher);

  function addBubble(role, text) {
    var b = el('div', 'pcw-msg pcw-' + role);
    renderText(b, text);
    list.appendChild(b);
    list.scrollTop = list.scrollHeight;
    return b;
  }

  function renderChips() {
    chips.innerHTML = '';
    if (state.messages.some(function (m) { return m.role === 'user'; })) return;
    cfg.suggestions.forEach(function (s) {
      var c = el('button', 'pcw-chip', s);
      c.type = 'button';
      c.addEventListener('click', function () { submit(s); });
      chips.appendChild(c);
    });
  }

  function renderAll() {
    list.innerHTML = '';
    addBubble('assistant', cfg.greeting);
    state.messages.forEach(function (m) { addBubble(m.role, m.text); });
    renderChips();
  }

  function setOpen(open) {
    root.setAttribute('data-open', open ? 'true' : 'false');
    launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      setTimeout(function () { input.focus(); }, 50);
      list.scrollTop = list.scrollHeight;
    }
  }

  function csrf() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function submit(text) {
    text = (text || '').trim();
    if (!text || sending) return;
    sending = true;
    send.disabled = true;
    input.value = '';
    autosize();

    state.messages.push({ role: 'user', text: text });
    addBubble('user', text);
    renderChips();
    save();

    var typing = el('div', 'pcw-msg pcw-assistant pcw-typing');
    typing.innerHTML = '<span></span><span></span><span></span>';
    typing.setAttribute('aria-label', 'Escribiendo');
    list.appendChild(typing);
    list.scrollTop = list.scrollHeight;

    fetch(cfg.endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf(),
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        message: text,
        conversation_id: state.conversationId,
        page_url: location.pathname,
      }),
    })
      .then(function (res) {
        return res.json().catch(function () { return {}; }).then(function (data) {
          if (res.status === 429) {
            return { reply: 'Estás enviando mensajes muy rápido. Espera un momento e inténtalo de nuevo.' };
          }
          if (res.status === 419) {
            return { reply: 'La sesión expiró. Recarga la página para seguir conversando.' };
          }
          return data;
        });
      })
      .catch(function () {
        return { reply: 'No hay conexión en este momento. Revisa tu internet e inténtalo de nuevo.' };
      })
      .then(function (data) {
        typing.remove();
        if (data.conversation_id) state.conversationId = data.conversation_id;
        var reply = data.reply || (data.message ? data.message : 'No pude responder. Inténtalo de nuevo.');
        state.messages.push({ role: 'assistant', text: reply });
        addBubble('assistant', reply);
        save();
        if (data.lead_captured && window.dataLayer) {
          window.dataLayer.push({ event: 'chatbot_lead' });
        }
        if (data.lead_captured && typeof window.fbq === 'function') {
          window.fbq('track', 'Lead');
        }
      })
      .finally(function () {
        sending = false;
        send.disabled = false;
        input.focus();
      });
  }

  function autosize() {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 120) + 'px';
  }

  launcher.addEventListener('click', function () {
    setOpen(root.getAttribute('data-open') !== 'true');
  });
  close.addEventListener('click', function () { setOpen(false); launcher.focus(); });
  form.addEventListener('submit', function (e) { e.preventDefault(); submit(input.value); });
  input.addEventListener('input', autosize);
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submit(input.value); }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && root.getAttribute('data-open') === 'true') setOpen(false);
  });

  // Cualquier elemento con data-open-chat abre el asistente (ej. botones del sitio).
  document.addEventListener('click', function (e) {
    var t = e.target.closest && e.target.closest('[data-open-chat]');
    if (t) { e.preventDefault(); setOpen(true); }
  });

  function mount() {
    document.body.appendChild(root);
    renderAll();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount);
  else mount();
})();
