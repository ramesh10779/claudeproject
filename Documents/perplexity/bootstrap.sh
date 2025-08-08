#!/usr/bin/env bash
set -e

echo "Scaffolding CKAD Practice Platform..."

# 1. Create directories
mkdir -p backend/{routes,bin,config}
mkdir -p frontend/{src,public}
mkdir -p deployment/{k8s}
mkdir -p docs

# 2. Create .env.example
cat > .env.example << 'EOV'
SUPABASE_URL=https://<project-ref>.supabase.co
SUPABASE_ANON_KEY=<anon-key>
SUPABASE_SERVICE_KEY=<service-key>
DB_TEMP_USER=ckad_temp
DB_TEMP_PASSWORD=TempPass123!
FRONTEND_URL=http://localhost:3000
BACKEND_URL=http://localhost:3001
NODE_ENV=development
PORT=3001
EOV

# 3. Create docker-compose.yml
cat > docker-compose.yml << 'EOC'
version: '3.8'
services:
  backend:
    build: ./backend
    ports:
      - "3001:3001"
    environment:
      - SUPABASE_URL=\${SUPABASE_URL}
      - SUPABASE_ANON_KEY=\${SUPABASE_ANON_KEY}
      - SUPABASE_SERVICE_KEY=\${SUPABASE_SERVICE_KEY}
      - BACKEND_URL=\${BACKEND_URL}
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
  frontend:
    build: ./frontend
    ports:
      - "3000:3000"
    environment:
      - VITE_SUPABASE_URL=\${SUPABASE_URL}
      - VITE_SUPABASE_ANON_KEY=\${SUPABASE_ANON_KEY}
    depends_on:
      - backend
EOC

# 4. Create minimal backend files
cat > backend/package.json << 'EOBF'
{
  "name": "ckad-practice-backend",
  "version": "1.0.0",
  "main": "server.js",
  "scripts": {
    "start": "node server.js"
  },
  "dependencies": {
    "express": "^4.18.2",
    "cors": "^2.8.5",
    "helmet": "^7.0.0",
    "@supabase/supabase-js": "^2.38.0",
    "dockerode": "^3.3.5",
    "dotenv": "^16.3.1"
  }
}
EOBF

cat > backend/server.js << 'EOBS'
require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const { createClient } = require('@supabase/supabase-js');
const Docker = require('dockerode');

const app = express();
const PORT = process.env.PORT || 3001;
const supabase = createClient(process.env.SUPABASE_URL, process.env.SUPABASE_SERVICE_KEY);
const docker = new Docker();
let simulators = {};

app.use(cors());
app.use(helmet());
app.use(express.json());

app.get('/health', (req, res) => res.json({ status: 'OK' }));

app.listen(PORT, () => console.log(`Backend listening on port ${PORT}`));
EOBS

# 5. Create minimal frontend files
cat > frontend/package.json << 'EOFPF'
{
  "name": "ckad-practice-frontend",
  "version": "1.0.0",
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^4.0.3",
    "vite": "^4.4.5"
  }
}
EOFPF

cat > frontend/vite.config.js << 'EOFV'
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  server: { port: 3000, proxy: { '/api': 'http://localhost:3001' } },
  plugins: [react()]
});
EOFV

cat > frontend/src/App.jsx << 'EOFA'
import React from 'react';
export default function App() {
  return <h1>CKAD Practice Platform</h1>;
}
EOFA

mkdir -p frontend/public
cat > frontend/public/index.html << 'EOFI'
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"/><title>CKAD Practice Platform</title></head>
<body><div id="root"></div><script type="module" src="/src/main.jsx"></script></body>
</html>
EOFI

cat > frontend/src/main.jsx << 'EOM'
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
ReactDOM.createRoot(document.getElementById('root')).render(<App />);
EOM

chmod +x bootstrap.sh
echo "Bootstrap script created. Run './bootstrap.sh' to scaffold all files."
