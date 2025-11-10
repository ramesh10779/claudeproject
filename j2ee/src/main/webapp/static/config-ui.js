// Copied/adapted client logic to run inside J2EE WAR
// This keeps the client-side experience and adds server-backed export endpoints when available.

(function() {
  const state = {
    envOrder: ["Development"],
    envConfigs: { "Development": {} },
    activeEnv: "Development",
  };

  const el = (id) => document.getElementById(id);

  function setStatus(msg) { const s = el('statusMessage'); if (s) s.textContent = msg || ''; }

  function renderEnvBadges() {
    const container = document.getElementById('envBadges');
    if (!container) return;
    container.innerHTML = '';
    state.envOrder.forEach(env => {
      const b = document.createElement('button');
      b.className = 'btn';
      b.textContent = env;
      if (env === state.activeEnv) b.classList.add('btn-primary');
      b.addEventListener('click', () => { state.activeEnv = env; renderAll(); });
      container.appendChild(b);
    });
  }

  function renderPairs() {
    const cont = document.getElementById('pairsContainer');
    if (!cont) return;
    cont.innerHTML = '';
    const kv = state.envConfigs[state.activeEnv] || {};
    Object.entries(kv).forEach(([k, v]) => {
      const row = document.createElement('div');
      row.style.display = 'flex'; row.style.gap = '8px'; row.style.marginBottom = '8px';
      const kIn = document.createElement('input'); kIn.value = k; kIn.placeholder = 'Key';
      const vIn = document.createElement('input'); vIn.value = v; vIn.placeholder = 'Value';
      const del = document.createElement('button'); del.className = 'btn'; del.textContent = 'Delete';
      del.addEventListener('click', () => { delete state.envConfigs[state.activeEnv][k]; renderAll(); });
      kIn.addEventListener('input', () => {
        const oldVal = state.envConfigs[state.activeEnv][k];
        delete state.envConfigs[state.activeEnv][k];
        state.envConfigs[state.activeEnv][kIn.value] = vIn.value;
      });
      vIn.addEventListener('input', () => { state.envConfigs[state.activeEnv][kIn.value] = vIn.value; });
      row.append(kIn, vIn, del);
      cont.appendChild(row);
    });
  }

  function renderStats() {
    const kv = state.envConfigs[state.activeEnv] || {};
    const keyCount = Object.keys(kv).length;
    const envTotal = state.envOrder.length;
    const activeEnvStat = el('activeEnvStat');
    const keyCountStat = el('keyCountStat');
    const envTotalStat = el('envTotalStat');
    if (activeEnvStat) activeEnvStat.textContent = `Active: ${state.activeEnv}`;
    if (keyCountStat) keyCountStat.textContent = `Keys: ${keyCount}`;
    if (envTotalStat) envTotalStat.textContent = `Environments: ${envTotal}`;
  }

  function renderAll() { renderEnvBadges(); renderPairs(); renderStats(); }

  function uniqueEnvName(base) {
    let i = 1; let name = base;
    const set = new Set(state.envOrder);
    while (set.has(name)) { name = `${base} ${i++}`; }
    return name;
  }

  function addPair() {
    const kv = state.envConfigs[state.activeEnv];
    let base = 'KEY'; let key = base; let i = 1;
    while (kv[key] !== undefined) { key = `${base}_${i++}`; }
    kv[key] = '';
    renderAll();
  }

  function addEnvironment() {
    const name = uniqueEnvName('Environment');
    state.envOrder.push(name);
    state.envConfigs[name] = {};
    state.activeEnv = name;
    renderAll();
  }

  function deleteEnvironment() {
    if (state.envOrder.length <= 1) { setStatus('At least one environment is required.'); return; }
    const idx = state.envOrder.indexOf(state.activeEnv);
    state.envOrder.splice(idx, 1);
    delete state.envConfigs[state.activeEnv];
    state.activeEnv = state.envOrder[Math.max(0, idx - 1)] || state.envOrder[0];
    renderAll();
  }

  function duplicateEnvironment() {
    const name = uniqueEnvName(`${state.activeEnv} Copy`);
    const src = state.envConfigs[state.activeEnv] || {};
    state.envConfigs[name] = { ...src };
    state.envOrder.push(name);
    state.activeEnv = name;
    renderAll();
  }

  function sortEnvironmentsAZ() {
    state.envOrder.sort((a, b) => a.localeCompare(b));
    renderAll();
  }

  // Import YAML (client-side)
  function openYAML() { const f = el('yamlFileInput'); if (!f) return; f.click(); }
  function parseGeneratedYAML(text) {
    const lines = text.split(/\r?\n/);
    const envOrder = []; const envConfigs = {};
    let current = null;
    for (const line of lines) {
      if (!line.trim()) continue;
      if (/^[^\s].*:/.test(line)) { // env header
        current = line.split(':')[0].trim();
        envOrder.push(current); envConfigs[current] = {};
      } else if (/^\s+[^:]+:/.test(line) && current) {
        const m = line.trim().match(/([^:]+):\s*(.*)$/);
        if (m) envConfigs[current][m[1]] = m[2];
      }
    }
    state.envOrder = envOrder.length ? envOrder : ["Development"]; state.envConfigs = envConfigs; state.activeEnv = state.envOrder[0];
    renderAll(); setStatus('YAML loaded.');
  }

  // Import XML (client-side)
  function openXML() { const f = el('xmlFileInput'); if (!f) return; f.click(); }
  function parseGeneratedXML(text) {
    const parser = new DOMParser(); const xml = parser.parseFromString(text, 'text/xml');
    let envEls = Array.from(xml.getElementsByTagName('environment'));
    const envOrder = []; const envConfigs = {};
    const getText = (el) => (el && el.textContent ? el.textContent.trim() : '');

    if (envEls.length) {
      // Standard format
      envEls.forEach(envEl => {
        const name = envEl.getAttribute('name') || 'Environment';
        envOrder.push(name); envConfigs[name] = {};
        const pairs = Array.from(envEl.querySelectorAll('pair, parameter, param'));
        pairs.forEach(p => {
          let key = p.getAttribute('key') || getText(p.getElementsByTagName('key')[0]) || p.getAttribute('scope-value') || p.getAttribute('name') || 'KEY';
          let value = p.getAttribute('value') || getText(p.getElementsByTagName('value')[0]) || getText(p);
          if (!key || key === '') {
            const child = p.querySelector('parameter, pair, param');
            if (child) {
              key = child.getAttribute('key') || getText(child.getElementsByTagName('key')[0]) || child.getAttribute('scope-value') || child.getAttribute('name') || key;
              value = child.getAttribute('value') || getText(child.getElementsByTagName('value')[0]) || value;
            }
          }
          if (key && key !== '') { envConfigs[name][key] = value || ''; }
        });
      });
    } else {
      // Fallback: build environments from top-level nodes and managedInstance elements
      const root = xml.documentElement;
      Array.from(root.children).forEach(node => {
        let envName = node.tagName;
        if (node.tagName.toLowerCase() === 'managedinstance') {
          envName = node.getAttribute('scope-value') || node.getAttribute('name') || envName;
        }
        envName = (envName || '').trim(); if (!envName) return;
        if (!envConfigs[envName]) { envConfigs[envName] = {}; envOrder.push(envName); }
        Array.from(node.querySelectorAll('pair, parameter, param')).forEach(p => {
          let key = p.getAttribute('key') || getText(p.getElementsByTagName('key')[0]) || p.getAttribute('scope-value') || p.getAttribute('name') || p.tagName;
          let value = p.getAttribute('value') || getText(p.getElementsByTagName('value')[0]) || getText(p);
          if (key) envConfigs[envName][key] = value || '';
        });
        // Nested managedInstance environments
        Array.from(node.getElementsByTagName('managedInstance')).forEach(mi => {
          const miName = (mi.getAttribute('scope-value') || mi.getAttribute('name') || 'managedInstance').trim();
          if (!envConfigs[miName]) { envConfigs[miName] = {}; envOrder.push(miName); }
          Array.from(mi.querySelectorAll('pair, parameter, param')).forEach(p => {
            let key = p.getAttribute('key') || getText(p.getElementsByTagName('key')[0]) || p.getAttribute('scope-value') || p.getAttribute('name') || p.tagName;
            let value = p.getAttribute('value') || getText(p.getElementsByTagName('value')[0]) || getText(p);
            if (key) envConfigs[miName][key] = value || '';
          });
        });
      });
    }

    state.envOrder = envOrder.length ? envOrder : ["Development"]; state.envConfigs = envConfigs; state.activeEnv = state.envOrder[0];
    renderAll(); setStatus('XML loaded.');
  }

  // Export YAML via server if available, else client-side
  async function exportYAML() {
    const payload = { envOrder: state.envOrder, envConfigs: state.envConfigs };
    try {
      const res = await fetch('api/export/yaml', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      if (res.ok) {
        const blob = await res.blob(); const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'config.yaml'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        setStatus('Exported YAML (server)'); return;
      }
    } catch (e) { /* fall back */ }
    // Client-side fallback
    let content = '';
    state.envOrder.forEach(env => {
      content += `${env}:\n`;
      const kv = state.envConfigs[env] || {};
      Object.entries(kv).forEach(([k, v]) => { content += `  ${k}: ${v}\n`; });
      content += `\n`;
    });
    const blob = new Blob([content], { type: 'application/x-yaml' });
    const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = 'config.yaml'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    setStatus('Exported YAML (client)');
  }

  // Export XML via server if available, else client-side
  async function exportXML() {
    const payload = { envOrder: state.envOrder, envConfigs: state.envConfigs };
    try {
      const res = await fetch('api/export/xml', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      if (res.ok) {
        const blob = await res.blob(); const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'config.xml'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        setStatus('Exported XML (server)'); return;
      }
    } catch (e) { /* fall back */ }
    // Client-side fallback
    const esc = (s) => (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    let xml = '<?xml version="1.0" encoding="UTF-8"?>\n<environments>\n';
    state.envOrder.forEach(env => {
      xml += `  <environment name="${esc(env)}">\n`;
      const kv = state.envConfigs[env] || {};
      Object.entries(kv).forEach(([k, v]) => { xml += `    <pair key="${esc(k)}" value="${esc(v)}"/>\n`; });
      xml += '  </environment>\n';
    });
    xml += '</environments>\n';
    const blob = new Blob([xml], { type: 'application/xml' });
    const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = 'config.xml'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    setStatus('Exported XML (client)');
  }

  // Wire controls
  function wire() {
    const map = {
      addPairBtn: addPair,
      heroAddPairBtn: addPair,
      addEnvironmentBtn: addEnvironment,
      deleteEnvironmentBtn: deleteEnvironment,
      duplicateEnvironmentBtn: duplicateEnvironment,
      sortEnvironmentsBtn: sortEnvironmentsAZ,
      exportYamlBtn: exportYAML,
      heroExportYamlBtn: exportYAML,
      exportXmlBtn: exportXML,
      heroExportXmlBtn: exportXML,
      exportXmlSidebarBtn: exportXML,
      openYamlBtn: openYAML,
      openXmlBtn: openXML,
    };
    Object.entries(map).forEach(([id, fn]) => { const b = el(id); if (b) b.addEventListener('click', fn); });
    const yamlInput = el('yamlFileInput'); if (yamlInput) yamlInput.addEventListener('change', (e) => {
      const file = e.target.files[0]; if (!file) return; const reader = new FileReader(); reader.onload = () => parseGeneratedYAML(reader.result); reader.readAsText(file);
    });
    const xmlInput = el('xmlFileInput'); if (xmlInput) xmlInput.addEventListener('change', (e) => {
      const file = e.target.files[0]; if (!file) return; const reader = new FileReader(); reader.onload = () => parseGeneratedXML(reader.result); reader.readAsText(file);
    });
  }

  document.addEventListener('DOMContentLoaded', () => { wire(); renderAll(); });
})();
