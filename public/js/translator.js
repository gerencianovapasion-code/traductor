/* Yaiza Translate — live audio translation engine (Web Speech API + server translate proxy) */
(function () {
  'use strict';

  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  const synth = window.speechSynthesis;
  const cfg = window.TRANSLATOR || { strings: {} };

  const micBtn = document.getElementById('micBtn');
  const statusEl = document.getElementById('status');
  const transcriptEl = document.getElementById('transcript');
  const outputEl = document.getElementById('output');
  const sourceSel = document.getElementById('sourceLang');
  const targetSel = document.getElementById('targetLang');
  const swapBtn = document.getElementById('swapBtn');
  const autoSpeak = document.getElementById('autoSpeak');

  if (!SR) {
    const u = document.getElementById('unsupported');
    if (u) u.style.display = 'block';
    if (micBtn) micBtn.disabled = true;
    return;
  }

  let recognition = null;
  let listening = false;
  let startedAt = 0;
  let secondsBuffer = 0;
  let voices = [];

  function loadVoices() { voices = synth ? synth.getVoices() : []; }
  loadVoices();
  if (synth && synth.onvoiceschanged !== undefined) synth.onvoiceschanged = loadVoices;

  function setStatus(text, live) {
    statusEl.textContent = text;
    micBtn.classList.toggle('live', !!live);
  }

  function recognitionLang() {
    const v = sourceSel.value;
    if (v && v !== 'auto') return v;
    return navigator.language || 'en-US';
  }

  function sourceCode() {
    const opt = sourceSel.options[sourceSel.selectedIndex];
    if (sourceSel.value === 'auto') return 'auto';
    return opt.getAttribute('data-code') || sourceSel.value.split('-')[0];
  }

  function targetCode() { return targetSel.value; }
  function targetSpeech() {
    const opt = targetSel.options[targetSel.selectedIndex];
    return opt ? (opt.getAttribute('data-speech') || targetSel.value) : targetSel.value;
  }

  function pickVoice(speechCode) {
    if (!voices.length) loadVoices();
    const base = speechCode.split('-')[0].toLowerCase();
    return voices.find(v => v.lang.toLowerCase() === speechCode.toLowerCase())
        || voices.find(v => v.lang.toLowerCase().startsWith(base))
        || null;
  }

  function speak(text) {
    if (!synth || !text) return;
    const u = new SpeechSynthesisUtterance(text);
    const sc = targetSpeech();
    u.lang = sc;
    const v = pickVoice(sc);
    if (v) u.voice = v;
    u.rate = 1; u.pitch = 1;
    synth.speak(u);
  }

  async function translate(text) {
    try {
      const res = await fetch(cfg.routes && cfg.routes.translate || window.APP.routes.translate, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.APP.csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ text: text, source: sourceCode(), target: targetCode() })
      });
      const data = await res.json();
      return data.text || text;
    } catch (e) {
      return text;
    }
  }

  async function logUsage(seconds) {
    if (!seconds) return;
    try {
      await fetch(window.APP.routes.usage, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.APP.csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ source: sourceCode(), target: targetCode(), seconds: Math.round(seconds), engine: (cfg.plan && cfg.plan.engine) || 'browser' })
      });
    } catch (e) {}
  }

  function buildRecognition() {
    const r = new SR();
    r.lang = recognitionLang();
    r.continuous = true;
    r.interimResults = true;
    r.maxAlternatives = 1;

    r.onresult = async (event) => {
      let interim = '';
      for (let i = event.resultIndex; i < event.results.length; i++) {
        const res = event.results[i];
        const text = res[0].transcript;
        if (res.isFinal) {
          transcriptEl.innerHTML = '<span>' + escapeHtml(text) + '</span>';
          outputEl.innerHTML = '<span class="muted">…</span>';
          const translated = await translate(text.trim());
          outputEl.textContent = translated;
          if (autoSpeak.checked) speak(translated);
        } else {
          interim += text;
        }
      }
      if (interim) transcriptEl.innerHTML = '<span class="partial">' + escapeHtml(interim) + '</span>';
    };

    r.onerror = (e) => {
      if (e.error === 'no-speech' || e.error === 'aborted') return;
      setStatus(cfg.strings.error || 'Error', false);
    };

    r.onend = () => {
      // Keep listening until the user stops (recognition auto-stops on silence).
      if (listening) {
        try { r.start(); } catch (e) {}
      }
    };

    return r;
  }

  function start() {
    recognition = buildRecognition();
    listening = true;
    startedAt = Date.now();
    try { recognition.start(); } catch (e) {}
    setStatus(cfg.strings.listening || 'Listening…', true);
    micBtn.textContent = '⏹️';
  }

  function stop() {
    listening = false;
    if (recognition) { try { recognition.stop(); } catch (e) {} }
    setStatus(cfg.strings.idle || 'Idle', false);
    micBtn.textContent = '🎙️';
    const elapsed = (Date.now() - startedAt) / 1000;
    secondsBuffer += elapsed;
    logUsage(secondsBuffer);
    secondsBuffer = 0;
  }

  micBtn.addEventListener('click', () => listening ? stop() : start());

  swapBtn.addEventListener('click', () => {
    // Swap source <-> target where possible (skip when source is auto).
    if (sourceSel.value === 'auto') return;
    const srcCode = sourceCode();
    const tgtCode = targetCode();
    for (const o of targetSel.options) if (o.value === srcCode) targetSel.value = srcCode;
    for (const o of sourceSel.options) if (o.getAttribute('data-code') === tgtCode) sourceSel.value = o.value;
  });

  // Periodic usage flush for long sessions.
  setInterval(() => {
    if (listening) {
      const elapsed = (Date.now() - startedAt) / 1000;
      if (elapsed >= 30) { secondsBuffer += elapsed; logUsage(secondsBuffer); secondsBuffer = 0; startedAt = Date.now(); }
    }
  }, 30000);

  function escapeHtml(s) {
    return s.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }
})();
