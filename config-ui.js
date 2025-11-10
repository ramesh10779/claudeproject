// Multi‑Stage Config Manager

const defaultEnvs = ["Development", "QA", "Staging", "Production"]; // 4 environments

const state = {
  envOrder: [...defaultEnvs],
  envConfigs: defaultEnvs.reduce((acc, env) => { acc[env] = {}; return acc; }, {}),
  activeEnv: defaultEnvs[0],
};

// DOM refs
const envTabsEl = document.getElementById("envTabs");
const activeEnvLabelEl = document.getElementById("activeEnvLabel");
const pairsTbodyEl = document.getElementById("pairsTbody");
const newConfigBtn = document.getElementById("newConfigBtn");
const openConfigBtn = document.getElementById("openConfigBtn");
const openFileInput = document.getElementById("openFileInput");
const openXmlBtn = document.getElementById("openXmlBtn");
const openXmlFileInput = document.getElementById("openXmlFileInput");
const exportConfigBtn = document.getElementById("exportConfigBtn");
const exportXmlBtn = document.getElementById("exportXmlBtn");
const addPairBtn = document.getElementById("addPairBtn");
const statusTextEl = document.getElementById("statusText");
const helpLink = document.getElementById("helpLink");
// Hero & badges
const getStartedBtn = document.getElementById("getStartedBtn");
const quickOpenBtn = document.getElementById("quickOpenBtn");
const quickExportBtn = document.getElementById("quickExportBtn");
const quickAddPairBtn = document.getElementById("quickAddPairBtn");
const envBadgesEl = document.getElementById("envBadges");
const visualActiveEnvEl = document.getElementById("visualActiveEnv");
const visualKeyCountEl = document.getElementById("visualKeyCount");
const visualEnvCountEl = document.getElementById("visualEnvCount");
// Sidebar feature refs
const sidebarNewConfigBtn = document.getElementById("sidebarNewConfigBtn");
const sidebarOpenConfigBtn = document.getElementById("sidebarOpenConfigBtn");
const sidebarExportConfigBtn = document.getElementById("sidebarExportConfigBtn");
const sidebarExportXmlBtn = document.getElementById("sidebarExportXmlBtn");
const sidebarAddPairBtn = document.getElementById("sidebarAddPairBtn");
const addEnvBtn = document.getElementById("addEnvBtn");
const deleteEnvBtn = document.getElementById("deleteEnvBtn");
const duplicateEnvBtn = document.getElementById("duplicateEnvBtn");
const sortEnvBtn = document.getElementById("sortEnvBtn");

function setStatus(msg) {
  statusTextEl.textContent = msg;
}

// Rendering
function renderTabs() {
  envTabsEl.innerHTML = "";
  state.envOrder.forEach((env) => {
    const btn = document.createElement("button");
    btn.className = `tab-btn${state.activeEnv === env ? " active" : ""}`;
    btn.setAttribute("type", "button");
    btn.title = "Double‑click to rename";
    const label = document.createElement("span");
    label.textContent = env;
    btn.appendChild(label);

    btn.addEventListener("click", () => {
      state.activeEnv = env;
      render();
    });

    btn.addEventListener("dblclick", () => {
      const newName = prompt("Rename environment:", env);
      if (!newName || newName.trim() === "") return;
      const trimmed = newName.trim();
      if (state.envOrder.includes(trimmed) && trimmed !== env) {
        alert("An environment with that name already exists.");
        return;
      }
      renameEnvironment(env, trimmed);
      render();
    });

    envTabsEl.appendChild(btn);
  });
}

function renderEditor() {
  activeEnvLabelEl.textContent = state.activeEnv;
  const kv = state.envConfigs[state.activeEnv] || {};
  pairsTbodyEl.innerHTML = "";

  const keys = Object.keys(kv);
  if (keys.length === 0) {
    // Render one empty row for user convenience
    addPairRow("", "");
    return;
  }
  keys.forEach((k) => addPairRow(k, kv[k]));
}

function addPairRow(key, value) {
  const tr = document.createElement("tr");

  const keyTd = document.createElement("td");
  const keyInput = document.createElement("input");
  keyInput.className = "kv-input";
  keyInput.placeholder = "KEY";
  keyInput.value = key;
  keyTd.appendChild(keyInput);

  const valueTd = document.createElement("td");
  const valueInput = document.createElement("input");
  valueInput.className = "kv-input";
  valueInput.placeholder = "VALUE";
  valueInput.value = value;
  valueTd.appendChild(valueInput);

  const actionsTd = document.createElement("td");
  actionsTd.className = "row-actions";
  const delBtn = document.createElement("button");
  delBtn.className = "btn danger";
  delBtn.textContent = "Trash";
  delBtn.addEventListener("click", () => {
    tr.remove();
    syncEditorToState();
  });
  actionsTd.appendChild(delBtn);

  tr.appendChild(keyTd);
  tr.appendChild(valueTd);
  tr.appendChild(actionsTd);

  keyInput.addEventListener("input", syncEditorToState);
  valueInput.addEventListener("input", syncEditorToState);

  pairsTbodyEl.appendChild(tr);
}

function syncEditorToState() {
  const kv = {};
  const rows = Array.from(pairsTbodyEl.querySelectorAll("tr"));
  rows.forEach((row) => {
    const inputs = row.querySelectorAll("input.kv-input");
    const k = inputs[0]?.value?.trim() ?? "";
    const v = inputs[1]?.value ?? "";
    if (k !== "") kv[k] = v;
  });
  state.envConfigs[state.activeEnv] = kv;
  setStatus(`Updated ${state.activeEnv} pairs.`);
}

function renderEnvBadges() {
  if (!envBadgesEl) return;
  envBadgesEl.innerHTML = "";
  state.envOrder.forEach((env) => {
    const count = Object.keys(state.envConfigs[env] || {}).length;
    const badge = document.createElement("div");
    badge.className = "env-badge";
    const nameSpan = document.createElement("span");
    nameSpan.className = "name";
    nameSpan.textContent = env;
    const countSpan = document.createElement("span");
    countSpan.className = "count";
    countSpan.textContent = `${count} key${count !== 1 ? "s" : ""}`;
    badge.appendChild(nameSpan);
    badge.appendChild(countSpan);
    badge.addEventListener("click", () => {
      state.activeEnv = env;
      render();
      setStatus(`Switched to ${env}.`);
    });
    envBadgesEl.appendChild(badge);
  });
  if (visualActiveEnvEl) visualActiveEnvEl.textContent = state.activeEnv;
  if (visualEnvCountEl) visualEnvCountEl.textContent = String(state.envOrder.length);
  if (visualKeyCountEl) visualKeyCountEl.textContent = String(Object.keys(state.envConfigs[state.activeEnv] || {}).length);
}

function render() {
  renderTabs();
  renderEditor();
  renderEnvBadges();
}

// Environment ops
function renameEnvironment(oldName, newName) {
  if (oldName === newName) return;
  const oldIndex = state.envOrder.indexOf(oldName);
  if (oldIndex === -1) return;
  // Move config
  const current = state.envConfigs[oldName] || {};
  delete state.envConfigs[oldName];
  state.envConfigs[newName] = current;
  // Update order
  state.envOrder[oldIndex] = newName;
  // Update active
  if (state.activeEnv === oldName) state.activeEnv = newName;
  setStatus(`Renamed environment '${oldName}' to '${newName}'.`);
}

function uniqueEnvName(base) {
  let name = base;
  let i = 2;
  while (state.envOrder.includes(name)) {
    name = `${base} ${i++}`;
  }
  return name;
}

function addEnvironment() {
  const base = "New Env";
  const name = uniqueEnvName(base);
  const idx = state.envOrder.indexOf(state.activeEnv);
  const insertAt = idx >= 0 ? idx + 1 : state.envOrder.length;
  state.envOrder.splice(insertAt, 0, name);
  state.envConfigs[name] = {};
  state.activeEnv = name;
  render();
  setStatus(`Added environment '${name}'.`);
}

function deleteEnvironment() {
  if (state.envOrder.length <= 1) { alert("At least one environment must remain."); return; }
  const env = state.activeEnv;
  const ok = confirm(`Delete environment '${env}'? This will remove its key/value pairs.`);
  if (!ok) return;
  const idx = state.envOrder.indexOf(env);
  if (idx === -1) return;
  state.envOrder.splice(idx, 1);
  delete state.envConfigs[env];
  state.activeEnv = state.envOrder[Math.max(0, idx - 1)] || state.envOrder[0];
  render();
  setStatus(`Deleted environment '${env}'.`);
}

function duplicateActiveEnvironment() {
  const src = state.activeEnv;
  const copyName = uniqueEnvName(`${src} Copy`);
  const idx = state.envOrder.indexOf(src);
  const insertAt = idx >= 0 ? idx + 1 : state.envOrder.length;
  state.envOrder.splice(insertAt, 0, copyName);
  state.envConfigs[copyName] = { ...state.envConfigs[src] };
  state.activeEnv = copyName;
  render();
  setStatus(`Duplicated '${src}' to '${copyName}'.`);
}

function sortEnvironmentsAZ() {
  const active = state.activeEnv;
  state.envOrder = [...state.envOrder].sort((a, b) => a.localeCompare(b));
  state.activeEnv = active;
  render();
  setStatus("Sorted environments A→Z.");
}

// File ops
function newConfig() {
  state.envOrder = [...defaultEnvs];
  state.envConfigs = defaultEnvs.reduce((acc, env) => { acc[env] = {}; return acc; }, {});
  state.activeEnv = defaultEnvs[0];
  render();
  setStatus("Initialized new config.");
}

function normalizeLoadedObject(obj) {
  // Accept either { environments: {Env: {...}} } or { Env: {...} }
  if (obj && typeof obj === "object" && obj.environments && typeof obj.environments === "object") {
    return obj.environments;
  }
  return obj;
}

function parseGeneratedYAML(text) {
  const envs = {};
  const order = [];
  const lines = text.split(/\r?\n/);
  let inEnvs = false;
  let currentEnv = null;
  const unquote = (s) => {
    const t = s.trim();
    if (t.startsWith('"') && t.endsWith('"')) return t.slice(1, -1).replace(/\\"/g, '"');
    return t;
  };
  for (let raw of lines) {
    const line = raw.replace(/\t/g, '  ');
    if (!line.trim() || line.trim().startsWith('#')) continue;
    if (!inEnvs) {
      if (/^environments\s*:\s*$/.test(line)) { inEnvs = true; }
      continue;
    }
    const envMatch = line.match(/^\s{2}([^:]+)\s*:\s*$/);
    if (envMatch) {
      currentEnv = unquote(envMatch[1]);
      if (!(currentEnv in envs)) { envs[currentEnv] = {}; order.push(currentEnv); }
      continue;
    }
    const kvMatch = line.match(/^\s{4}([^:]+)\s*:\s*(.*)$/);
    if (kvMatch && currentEnv) {
      const key = unquote(kvMatch[1]);
      let val = unquote(kvMatch[2] || "");
      envs[currentEnv][key] = val;
      continue;
    }
  }
  return { envs, order };
}

function openYAML(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      const text = e.target.result;
      const { envs, order } = parseGeneratedYAML(text);
      if (!order.length) throw new Error("No environments found in YAML.");
      state.envOrder = order;
      state.envConfigs = envs;
      state.activeEnv = order[0];
      render();
      setStatus(`Loaded config with ${order.length} environment(s).`);
    } catch (err) {
      console.error(err);
      alert("Failed to parse YAML config. Ensure it matches the exported format.");
      setStatus("Error loading file.");
    }
  };
  reader.readAsText(file);
}

function parseGeneratedXML(text) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(text, "application/xml");
  // Detect parse errors
  if (doc.getElementsByTagName("parsererror").length) {
    throw new Error("Invalid XML");
  }
  const envs = {};
  const order = [];
  const root = doc.getElementsByTagName("environments")[0] || doc.documentElement;
  if (!root) throw new Error("Missing XML root");

  const getText = (el) => (el && el.textContent != null ? el.textContent.trim() : "");

  Array.from(root.children).forEach((envNode) => {
    let envName = envNode.tagName;
    const tagLower = envNode.tagName.toLowerCase();
    if (tagLower === "environment") {
      envName = envNode.getAttribute("name") || envNode.getAttribute("id") || envNode.getAttribute("env") || "Environment";
    } else if (tagLower === "managedinstance") {
      envName = envNode.getAttribute("scope-value") || envNode.getAttribute("name") || envName;
    }
    if (!envName || envName.trim() === "") return;
    envName = envName.trim();
    if (!(envName in envs)) { envs[envName] = {}; order.push(envName); }

    // Collect parameters and pairs anywhere under this node
    Array.from(envNode.querySelectorAll("pair, parameter, param")).forEach((kvNode) => {
      const lower = kvNode.tagName.toLowerCase();
      let key = kvNode.getAttribute("key") || getText(kvNode.getElementsByTagName("key")[0]) || kvNode.tagName;
      // Consider scope-value/name attributes for <parameter>
      if ((!key || key.trim() === "") && (lower === "parameter" || lower === "param")) {
        key = kvNode.getAttribute("scope-value") || kvNode.getAttribute("name") || key;
      }
      let val = kvNode.getAttribute("value")
        || getText(kvNode.getElementsByTagName("value")[0])
        || getText(kvNode);
      // Fallback: inspect first child parameter/pair if missing
      if ((!key || key.trim() === "") || (val == null || val === "")) {
        const childParam = kvNode.getElementsByTagName("parameter")[0]
          || kvNode.getElementsByTagName("pair")[0]
          || kvNode.getElementsByTagName("param")[0];
        if (childParam) {
          const childKeyAttr = childParam.getAttribute("key");
          const childValAttr = childParam.getAttribute("value");
          const childKeyEl = childParam.getElementsByTagName("key")[0];
          const childValEl = childParam.getElementsByTagName("value")[0];
          key = childKeyAttr || getText(childKeyEl) || key;
          val = childValAttr || getText(childValEl) || val;
          if ((!key || key.trim() === "") && (childParam.tagName.toLowerCase() === "parameter" || childParam.tagName.toLowerCase() === "param")) {
            key = childParam.getAttribute("scope-value") || childParam.getAttribute("name") || key;
          }
        }
      }
      if (!key || key.trim() === "") return;
      envs[envName][key.trim()] = val;
    });

    // Create separate environments for nested managedInstance elements
    Array.from(envNode.getElementsByTagName("managedInstance")).forEach((mi) => {
      const miName = (mi.getAttribute("scope-value") || mi.getAttribute("name") || "managedInstance").trim();
      if (!(miName in envs)) { envs[miName] = {}; order.push(miName); }
      Array.from(mi.querySelectorAll("pair, parameter, param")).forEach((kvNode) => {
        let key = kvNode.getAttribute("key") || getText(kvNode.getElementsByTagName("key")[0]) || kvNode.getAttribute("scope-value") || kvNode.getAttribute("name") || kvNode.tagName;
        let val = kvNode.getAttribute("value") || getText(kvNode.getElementsByTagName("value")[0]) || getText(kvNode);
        if (!key || key.trim() === "") return;
        envs[miName][key.trim()] = val;
      });
    });
  });
  return { envs, order };
}

function openXML(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      const text = e.target.result;
      const { envs, order } = parseGeneratedXML(text);
      if (!order.length) throw new Error("No environments found in XML.");
      state.envOrder = order;
      state.envConfigs = envs;
      state.activeEnv = order[0];
      render();
      setStatus(`Loaded XML with ${order.length} environment(s).`);
    } catch (err) {
      console.error(err);
      alert("Failed to parse XML config. Ensure it matches the supported format.");
      setStatus("Error loading XML file.");
    }
  };
  reader.readAsText(file);
}

function yamlQuote(str) {
  const s = String(str);
  const needsQuote = /[#:>\-\[\]{},&*!?|>'"%@`\s]/.test(s) || s === "" || /^\d/.test(s);
  if (needsQuote) return '"' + s.replace(/"/g, '\\"') + '"';
  return s;
}

function exportYAML() {
  let lines = [];
  lines.push("environments:");
  state.envOrder.forEach((env) => {
    lines.push(`  ${yamlQuote(env)}:`);
    const kv = state.envConfigs[env] || {};
    Object.keys(kv).forEach((k) => {
      const v = kv[k] ?? "";
      lines.push(`    ${yamlQuote(k)}: ${yamlQuote(v)}`);
    });
  });
  const yamlText = lines.join("\n") + "\n";
  const blob = new Blob([yamlText], { type: "text/yaml" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "config.yaml";
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
  setStatus("Exported config.yaml.");
}

function xmlEscapeAttr(str) {
  const s = String(str);
  return s
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&apos;");
}

function exportXML() {
  const lines = [];
  lines.push('<?xml version="1.0" encoding="UTF-8"?>');
  lines.push('<environments>');
  state.envOrder.forEach((env) => {
    lines.push(`  <environment name="${xmlEscapeAttr(env)}">`);
    const kv = state.envConfigs[env] || {};
    Object.keys(kv).forEach((k) => {
      const v = kv[k] ?? "";
      lines.push(`    <pair key="${xmlEscapeAttr(k)}" value="${xmlEscapeAttr(v)}"/>`);
    });
    lines.push('  </environment>');
  });
  lines.push('</environments>');
  const xmlText = lines.join("\n") + "\n";
  const blob = new Blob([xmlText], { type: "application/xml" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "config.xml";
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
  setStatus("Exported config.xml.");
}

function addEmptyPair() { addPairRow("", ""); }

// Help modal controls
function openKnowledgeBase() {
  window.location.href = "knowledgebase.html";
}

function switchEnv(delta) {
  const idx = state.envOrder.indexOf(state.activeEnv);
  if (idx === -1) return;
  let next = idx + delta;
  if (next < 0) next = state.envOrder.length - 1;
  if (next >= state.envOrder.length) next = 0;
  state.activeEnv = state.envOrder[next];
  render();
  setStatus(`Switched to ${state.activeEnv}.`);
}

// Event wiring
newConfigBtn.addEventListener("click", newConfig);
openConfigBtn.addEventListener("click", () => openFileInput.click());
openFileInput.addEventListener("change", (e) => {
  const [file] = e.target.files || [];
  if (file) openYAML(file);
  openFileInput.value = ""; // reset
});
exportConfigBtn.addEventListener("click", exportYAML);
if (exportXmlBtn) exportXmlBtn.addEventListener("click", exportXML);
addPairBtn.addEventListener("click", () => { addEmptyPair(); });
// XML open wiring
if (openXmlBtn) openXmlBtn.addEventListener("click", () => openXmlFileInput && openXmlFileInput.click());
if (openXmlFileInput) openXmlFileInput.addEventListener("change", (e) => {
  const [file] = e.target.files || [];
  if (file) openXML(file);
  openXmlFileInput.value = "";
});
// Hero quick actions
if (getStartedBtn) getStartedBtn.addEventListener("click", newConfig);
if (quickOpenBtn) quickOpenBtn.addEventListener("click", () => openFileInput.click());
if (quickExportBtn) quickExportBtn.addEventListener("click", exportYAML);
if (quickAddPairBtn) quickAddPairBtn.addEventListener("click", addEmptyPair);
// Sidebar actions
if (sidebarNewConfigBtn) sidebarNewConfigBtn.addEventListener("click", newConfig);
if (sidebarOpenConfigBtn) sidebarOpenConfigBtn.addEventListener("click", () => openFileInput.click());
if (sidebarExportConfigBtn) sidebarExportConfigBtn.addEventListener("click", exportYAML);
if (sidebarExportXmlBtn) sidebarExportXmlBtn.addEventListener("click", exportXML);
if (sidebarAddPairBtn) sidebarAddPairBtn.addEventListener("click", addEmptyPair);
if (addEnvBtn) addEnvBtn.addEventListener("click", addEnvironment);
if (deleteEnvBtn) deleteEnvBtn.addEventListener("click", deleteEnvironment);
if (duplicateEnvBtn) duplicateEnvBtn.addEventListener("click", duplicateActiveEnvironment);
if (sortEnvBtn) sortEnvBtn.addEventListener("click", sortEnvironmentsAZ);

// Knowledge Base link
if (helpLink) helpLink.addEventListener("click", (e) => {
  // allow default navigation
});

// Keyboard shortcuts
document.addEventListener("keydown", (e) => {
  const target = e.target;
  const isInput = target && (target.tagName === "INPUT" || target.tagName === "TEXTAREA");
  // '?' opens Knowledge Base (Shift + '/')
  if (e.key === "?" || (e.shiftKey && e.key === "/")) { openKnowledgeBase(); e.preventDefault(); return; }

  // Ctrl/Cmd shortcuts should work even when focused in inputs
  const mod = e.ctrlKey || e.metaKey;
  if (mod) {
    if (e.key.toLowerCase() === "n") { newConfig(); e.preventDefault(); return; }
    if (e.key.toLowerCase() === "o") { openFileInput.click(); e.preventDefault(); return; }
    if (e.key.toLowerCase() === "e") { exportYAML(); e.preventDefault(); return; }
    if (e.key === "Enter") { addEmptyPair(); e.preventDefault(); return; }
  }

  // Alt + arrows to switch env, avoid when typing in inputs
  if (!isInput && e.altKey) {
    if (e.key === "ArrowDown") { switchEnv(1); e.preventDefault(); return; }
    if (e.key === "ArrowUp") { switchEnv(-1); e.preventDefault(); return; }
  }
});

// Initial render
render();
setStatus("Ready.");
