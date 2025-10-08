<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Information - AITechHub Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
        }

        .header h1 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .back-link {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-icon {
            font-size: 1.8rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: #1f2937;
            font-size: 1rem;
            font-family: 'Courier New', monospace;
            background: #f3f4f6;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            word-break: break-all;
            position: relative;
        }

        .copy-btn {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: #667eea;
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .copy-btn:hover {
            background: #5568d3;
        }

        .link-value {
            color: #667eea;
            text-decoration: none;
            display: block;
        }

        .link-value:hover {
            text-decoration: underline;
        }

        .variables-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .variables-table th {
            background: #f9fafb;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .variables-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        .variables-table tr:hover {
            background: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-required {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .priority-high {
            color: #dc2626;
            font-weight: 700;
        }

        .priority-medium {
            color: #f59e0b;
            font-weight: 600;
        }

        .priority-low {
            color: #10b981;
        }

        .checklist {
            list-style: none;
        }

        .checklist-item {
            padding: 1rem;
            border-left: 4px solid #e5e7eb;
            margin-bottom: 0.75rem;
            background: #f9fafb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .checklist-item.completed {
            border-left-color: #10b981;
        }

        .checklist-item.in-progress {
            border-left-color: #3b82f6;
        }

        .checklist-item.pending {
            border-left-color: #f59e0b;
        }

        .checkbox {
            width: 20px;
            height: 20px;
            margin-right: 1rem;
        }

        .doc-list {
            list-style: none;
        }

        .doc-item {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .doc-title {
            font-weight: 600;
            color: #1f2937;
        }

        .doc-meta {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .quick-link {
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 1rem 1.5rem;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            margin-bottom: 2rem;
            color: #92400e;
        }

        .alert-success {
            background: #d1fae5;
            border-left-color: #10b981;
            color: #065f46;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .quick-links {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/admin/products" class="back-link">← Back to Admin Dashboard</a>

        <div class="header">
            <h1>🚀 Deployment Information Center</h1>
            <p>Complete deployment guide and configuration details for AITechHub Store</p>
        </div>

        <div class="alert">
            <strong>⚠️ Important:</strong> This page contains sensitive deployment information. Do not share these credentials publicly.
        </div>

        <!-- Quick Links -->
        <div class="card">
            <h2 class="card-title"><span class="card-icon">🔗</span> Quick Links</h2>
            <div class="quick-links">
                <a href="{{ $deploymentInfo['gitlab']['pipelines'] }}" target="_blank" class="quick-link">
                    📊 GitLab Pipelines
                </a>
                <a href="{{ $deploymentInfo['gitlab']['variables'] }}" target="_blank" class="quick-link">
                    ⚙️ GitLab Variables
                </a>
                <a href="{{ $deploymentInfo['hostinger']['panel'] }}" target="_blank" class="quick-link">
                    🌐 Hostinger Panel
                </a>
                <a href="{{ $deploymentInfo['hostinger']['file_manager'] }}" target="_blank" class="quick-link">
                    📁 File Manager
                </a>
                <a href="{{ $deploymentInfo['hostinger']['databases'] }}" target="_blank" class="quick-link">
                    🗄️ Databases
                </a>
                <a href="{{ $deploymentInfo['domain']['url'] }}" target="_blank" class="quick-link">
                    🌍 Live Site
                </a>
            </div>
        </div>

        <div class="grid">
            <!-- FTP Credentials -->
            <div class="card">
                <h2 class="card-title"><span class="card-icon">📡</span> FTP Credentials</h2>
                <div class="info-group">
                    <div class="info-label">FTP Host (IP)</div>
                    <div class="info-value">
                        {{ $deploymentInfo['ftp']['host'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['ftp']['host'] }}')">Copy</button>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">FTP Hostname</div>
                    <div class="info-value">
                        {{ $deploymentInfo['ftp']['hostname'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['ftp']['hostname'] }}')">Copy</button>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">FTP Username</div>
                    <div class="info-value">
                        {{ $deploymentInfo['ftp']['username'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['ftp']['username'] }}')">Copy</button>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Upload Path</div>
                    <div class="info-value">
                        {{ $deploymentInfo['ftp']['path'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['ftp']['path'] }}')">Copy</button>
                    </div>
                </div>
            </div>

            <!-- Database Credentials -->
            <div class="card">
                <h2 class="card-title"><span class="card-icon">🗄️</span> Database Credentials</h2>
                <div class="info-group">
                    <div class="info-label">Database Host</div>
                    <div class="info-value">
                        {{ $deploymentInfo['database']['host'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['database']['host'] }}')">Copy</button>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Database Name</div>
                    <div class="info-value">
                        {{ $deploymentInfo['database']['name'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['database']['name'] }}')">Copy</button>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Database Username</div>
                    <div class="info-value">
                        {{ $deploymentInfo['database']['username'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['database']['username'] }}')">Copy</button>
                    </div>
                </div>
                <div class="alert-success" style="margin-top: 1rem; padding: 0.75rem; border-radius: 8px;">
                    ✅ Database Created Successfully
                </div>
            </div>

            <!-- Domain Configuration -->
            <div class="card">
                <h2 class="card-title"><span class="card-icon">🌍</span> Domain Configuration</h2>
                <div class="info-group">
                    <div class="info-label">Live URL</div>
                    <div class="info-value">
                        <a href="{{ $deploymentInfo['domain']['url'] }}" target="_blank" class="link-value">
                            {{ $deploymentInfo['domain']['url'] }}
                        </a>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Document Root</div>
                    <div class="info-value">
                        {{ $deploymentInfo['domain']['document_root'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['domain']['document_root'] }}')">Copy</button>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding: 0.75rem; background: #fef3c7; border-radius: 8px; color: #92400e;">
                    ⚠️ Set this in Hostinger: Domains → aitechhub.store → Manage
                </div>
            </div>

            <!-- Security Keys -->
            <div class="card">
                <h2 class="card-title"><span class="card-icon">🔐</span> Security Keys</h2>
                <div class="info-group">
                    <div class="info-label">APP_KEY</div>
                    <div class="info-value">
                        {{ $deploymentInfo['keys']['app_key'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['keys']['app_key'] }}')">Copy</button>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">ADMIN_API_KEY</div>
                    <div class="info-value">
                        {{ $deploymentInfo['keys']['admin_api_key'] }}
                        <button class="copy-btn" onclick="copyToClipboard('{{ $deploymentInfo['keys']['admin_api_key'] }}')">Copy</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- GitLab Variables -->
        <div class="card">
            <h2 class="card-title"><span class="card-icon">⚙️</span> GitLab CI/CD Variables</h2>
            <p style="color: #6b7280; margin-bottom: 1rem;">
                Add these 8 variables in GitLab: Settings → CI/CD → Variables
            </p>
            <table class="variables-table">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gitlabVariables as $var)
                    <tr>
                        <td><strong>{{ $var['key'] }}</strong></td>
                        <td>{{ $var['value'] }}</td>
                        <td><span class="status-badge status-{{ $var['status'] }}">{{ $var['status'] }}</span></td>
                        <td>
                            <button class="copy-btn" onclick="copyToClipboard('{{ $var['value'] }}')">Copy</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="{{ $deploymentInfo['gitlab']['variables'] }}" target="_blank" class="btn">
                    Open GitLab Variables →
                </a>
            </div>
        </div>

        <!-- Deployment Checklist -->
        <div class="card">
            <h2 class="card-title"><span class="card-icon">✅</span> Deployment Checklist</h2>
            <ul class="checklist">
                @foreach($checklist as $item)
                <li class="checklist-item {{ $item['status'] }}">
                    <div style="display: flex; align-items: center; flex: 1;">
                        @if($item['status'] === 'completed')
                            <span style="color: #10b981; font-size: 1.5rem; margin-right: 1rem;">✓</span>
                        @elseif($item['status'] === 'in_progress')
                            <span style="color: #3b82f6; font-size: 1.5rem; margin-right: 1rem;">⏳</span>
                        @else
                            <span style="color: #f59e0b; font-size: 1.5rem; margin-right: 1rem;">○</span>
                        @endif
                        <span>{{ $item['task'] }}</span>
                    </div>
                    <span class="priority-{{ $item['priority'] }}">{{ strtoupper($item['priority']) }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Documentation -->
        <div class="card">
            <h2 class="card-title"><span class="card-icon">📚</span> Documentation</h2>
            <ul class="doc-list">
                @foreach($documentation as $doc)
                <li class="doc-item">
                    <div>
                        <div class="doc-title">📄 {{ $doc['title'] }}</div>
                        <div class="doc-meta">{{ $doc['file'] }} • {{ $doc['size'] }}</div>
                    </div>
                    <a href="/docs/{{ $doc['file'] }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                        View
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- SSH Commands -->
        <div class="card">
            <h2 class="card-title"><span class="card-icon">💻</span> SSH Commands</h2>
            <div class="info-group">
                <div class="info-label">Connect to Server</div>
                <div class="info-value">
                    ssh u631122123.aitechhub.store@aitechhub.store
                    <button class="copy-btn" onclick="copyToClipboard('ssh u631122123.aitechhub.store@aitechhub.store')">Copy</button>
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Navigate to Project</div>
                <div class="info-value">
                    cd /public_html
                    <button class="copy-btn" onclick="copyToClipboard('cd /public_html')">Copy</button>
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Run Migrations</div>
                <div class="info-value">
                    php artisan migrate --force
                    <button class="copy-btn" onclick="copyToClipboard('php artisan migrate --force')">Copy</button>
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Clear & Cache Config</div>
                <div class="info-value">
                    php artisan config:cache && php artisan route:cache && php artisan view:cache
                    <button class="copy-btn" onclick="copyToClipboard('php artisan config:cache && php artisan route:cache && php artisan view:cache')">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show temporary success message
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✓ Copied!';
                btn.style.background = '#10b981';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                }, 2000);
            }).catch(err => {
                alert('Failed to copy: ' + err);
            });
        }
    </script>
</body>
</html>
