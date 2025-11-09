<%@ page contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" %>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Multi-Stage Config Manager</title>
  <link rel="stylesheet" href="static/styles.css" />
</head>
<body>
  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-left">
      <h1 class="app-title">Multi-Stage Config Manager</h1>
    </div>
    <div class="topbar-right">
      <!-- Existing YAML controls -->
      <button id="openYamlBtn" class="btn">Open YAML</button>
      <input id="yamlFileInput" type="file" accept=".yaml,.yml" style="display:none" />
      <button id="exportYamlBtn" class="btn">Export YAML</button>

      <!-- XML controls -->
      <button id="openXmlBtn" class="btn">Open XML</button>
      <input id="xmlFileInput" type="file" accept=".xml" style="display:none" />
      <button id="exportXmlBtn" class="btn">Export XML</button>
    </div>
  </header>

  <!-- Layout -->
  <main class="layout">
    <aside class="sidebar">
      <section class="sidebar-section">
        <div class="sidebar-section-title">Quick Actions</div>
        <div class="sidebar-actions">
          <button id="newConfigBtn" class="btn btn-compact">New Config</button>
          <button id="openBtn" class="btn btn-compact">Open</button>
          <button id="exportBtn" class="btn btn-compact">Export</button>
          <button id="addPairBtn" class="btn btn-compact">Add Pair</button>
          <button id="exportXmlSidebarBtn" class="btn btn-compact">Export XML</button>
        </div>
      </section>
      <section class="sidebar-section">
        <div class="sidebar-section-title">Environment Tools</div>
        <div class="sidebar-actions">
          <button id="addEnvironmentBtn" class="btn btn-compact">Add Env</button>
          <button id="deleteEnvironmentBtn" class="btn btn-compact">Delete Env</button>
          <button id="duplicateEnvironmentBtn" class="btn btn-compact">Duplicate Env</button>
          <button id="sortEnvironmentsBtn" class="btn btn-compact">Sort A→Z</button>
        </div>
      </section>
    </aside>

    <section class="content">
      <section class="hero">
        <div class="hero-left">
          <h2 class="hero-title">Manage multi-stage environment configurations with ease</h2>
          <p class="hero-subtitle">Switch environments, edit keys, import YAML/XML, and export cleanly.</p>
          <div class="hero-actions">
            <button id="heroAddPairBtn" class="btn btn-primary">Add Pair</button>
            <button id="heroExportYamlBtn" class="btn">Export YAML</button>
            <button id="heroExportXmlBtn" class="btn">Export XML</button>
          </div>
        </div>
        <div class="hero-right">
          <div class="visual-card">
            <div class="visual-stat"><span id="activeEnvStat">Active: Development</span></div>
            <div class="visual-stat"><span id="keyCountStat">Keys: 0</span></div>
            <div class="visual-stat"><span id="envTotalStat">Environments: 1</span></div>
          </div>
        </div>
      </section>

      <section class="env-badges" id="envBadges"></section>

      <section class="editor">
        <div id="pairsContainer"></div>
      </section>
    </section>
  </main>

  <footer class="footer">
    <a href="knowledgebase.html" class="kb-link">Knowledge Base</a>
    <span id="statusMessage" class="status"></span>
  </footer>

  <script src="static/config-ui.js"></script>
</body>
</html>