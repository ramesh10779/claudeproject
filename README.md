# Multi-Stage Config Manager

[![Stars](https://img.shields.io/github/stars/ramesh10779/claudeproject?style=social)](https://github.com/ramesh10779/claudeproject)
![Client](https://img.shields.io/badge/Client-HTML%2FCSS%20%2B%20JS-yellow)
![Server](https://img.shields.io/badge/J2EE-WAR-blue)
![Status](https://img.shields.io/badge/Status-Active-success)

A simple, clean app to manage environment-based configuration pairs (keys/values) across multiple stages (Development, Staging, Production, etc.). It supports importing from YAML/XML, editing in the browser, switching environments quickly, and exporting to YAML/XML — either client-side or via J2EE servlets.

## TL;DR (Getting Started)
- Open `index.html` directly in a modern browser, or run `python3 -m http.server 8000` and visit `http://localhost:8000/`.
- Add environments, edit key/value pairs, import `.yaml/.yml` or `.xml`, then export YAML/XML.
- J2EE: `cd j2ee && mvn -DskipTests package` → deploy the WAR in Tomcat (`webapps/`).

## Features
- Manage environments: add, delete, duplicate, sort A→Z
- Environment badges: click to switch active environment
- Edit pairs: add/delete and inline edit of keys/values
- Import: load `.yaml`/`.yml` and `.xml` files
- Export: download YAML or XML with proper escaping
- J2EE WAR: optional server-side export (XML/YAML) via servlets
- Knowledge Base: helpful usage notes available from the footer

## Project Structure
```
.
├── index.html              # Standalone browser app
├── styles.css              # Global CSS with responsive layout
├── config-ui.js            # Client-side logic for environments and pairs
├── knowledgebase.html      # Usage notes and help
└── j2ee/                   # Maven WAR for J2EE deployment
    ├── pom.xml
    └── src/main/
        ├── java/com/example/config/
        │   ├── ConvertUtils.java         # XML/YAML helpers
        │   ├── ExportXmlServlet.java     # POST /api/export/xml → config.xml
        │   └── ExportYamlServlet.java    # POST /api/export/yaml → config.yaml
        └── webapp/
            ├── WEB-INF/web.xml           # Servlet mappings and welcome file
            ├── index.jsp                 # Ported UI running in a WAR
            ├── knowledgebase.html
            └── static/
                ├── styles.css
                └── config-ui.js
```

## Quick Start (Standalone)
- Requirements: any modern browser
- Optionally run a local static server (macOS):
  - `python3 -m http.server 8000`
  - Open `http://localhost:8000/`
  - Use the UI buttons: Open YAML/XML, Add Pair, Export YAML/XML

## J2EE Build & Deploy
- Requirements: Java 11+, Maven, a servlet container (Tomcat 9+)
- Build WAR:
  - `cd j2ee`
  - `mvn -DskipTests package`
- Deploy to Tomcat:
  - Copy `target/multistage-config-manager-1.0.0.war` to `TOMCAT_HOME/webapps/`
  - Visit: `http://localhost:8080/multistage-config-manager-1.0.0/`
- Server endpoints:
  - `POST /api/export/xml` → returns `config.xml`
  - `POST /api/export/yaml` → returns `config.yaml`
- Client automatically tries server export first, falling back to client-side export if unavailable.

## Usage
1. Add environments via sidebar: Add Env, Duplicate Env, Sort A→Z
2. Click environment badges to switch active env
3. Add/edit/delete key/value pairs in the editor
4. Import:
   - YAML: Click `Open YAML` and select a `.yaml/.yml` file
   - XML: Click `Open XML` and select a `.xml` file
5. Export:
   - YAML: Use `Export YAML` (topbar or hero)
   - XML: Use `Export XML` (topbar, hero, or sidebar)

## Data Formats
XML (export):
```xml
<?xml version="1.0" encoding="UTF-8"?>
<environments>
  <environment name="Development">
    <pair key="API_URL" value="http://dev.example.com"/>
    <pair key="TOKEN" value="dev-123"/>
  </environment>
</environments>
```

YAML (export):
```yaml
Development:
  API_URL: http://dev.example.com
  TOKEN: dev-123

Production:
  API_URL: https://api.example.com
  TOKEN: prod-abc
```

## Notes
- XML/YAML import parsers handle common structures; if your XML/YAML schema differs, provide a sample and we can extend the parsers.
- Client-side export uses safe escaping; server-side servlets apply consistent conversions via `ConvertUtils`.
- The UI is responsive: hero stacks, sidebar wraps actions, and badges avoid overlap at smaller widths.

## License
No license specified; treat as internal tooling unless a license is added.