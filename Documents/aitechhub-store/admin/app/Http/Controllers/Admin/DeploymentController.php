<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeploymentController extends Controller
{
    public function index(): View
    {
        // Deployment configuration details
        $deploymentInfo = [
            'ftp' => [
                'host' => '72.60.238.18',
                'hostname' => 'ftp.aitechhub.store',
                'username' => 'u631122123.aitechhub.store',
                'path' => '/public_html',
            ],
            'database' => [
                'host' => 'localhost',
                'name' => 'u631122123_aitech_hub',
                'username' => 'u631122123_admin_ceo',
            ],
            'domain' => [
                'url' => 'https://aitechhub.store',
                'document_root' => '/public_html/public',
            ],
            'gitlab' => [
                'repo' => 'https://gitlab.com/ramesh10779-group/ramesh10779-project',
                'pipelines' => 'https://gitlab.com/ramesh10779-group/ramesh10779-project/-/pipelines',
                'variables' => 'https://gitlab.com/ramesh10779-group/ramesh10779-project/-/settings/ci_cd',
            ],
            'hostinger' => [
                'panel' => 'https://hpanel.hostinger.com',
                'file_manager' => 'https://hpanel.hostinger.com/websites/aitechhub.store/file-manager',
                'databases' => 'https://hpanel.hostinger.com/websites/aitechhub.store/databases',
            ],
            'keys' => [
                'app_key' => 'base64:6iOKelCf8mYiPYaDpWzkd0tRRJ0Lfi9iMe1c2u5FEyE=',
                'admin_api_key' => 'Oohv67SsqgV6CfvMeOK7aFM+jRRXa2hC3Ke97CE6YVA=',
            ],
        ];

        // GitLab CI/CD variables needed
        $gitlabVariables = [
            ['key' => 'HOSTINGER_FTP_HOST', 'value' => '72.60.238.18', 'status' => 'required'],
            ['key' => 'HOSTINGER_FTP_USER', 'value' => 'u631122123.aitechhub.store', 'status' => 'required'],
            ['key' => 'HOSTINGER_FTP_PASS', 'value' => '[Your FTP Password]', 'status' => 'required'],
            ['key' => 'APP_KEY', 'value' => 'base64:6iOKelCf8mYiPYaDpWzkd0tRRJ0Lfi9iMe1c2u5FEyE=', 'status' => 'required'],
            ['key' => 'DB_DATABASE', 'value' => 'u631122123_aitech_hub', 'status' => 'required'],
            ['key' => 'DB_USERNAME', 'value' => 'u631122123_admin_ceo', 'status' => 'required'],
            ['key' => 'DB_PASSWORD', 'value' => 'Sasinikhilesh$03', 'status' => 'required'],
            ['key' => 'ADMIN_API_KEY', 'value' => 'Oohv67SsqgV6CfvMeOK7aFM+jRRXa2hC3Ke97CE6YVA=', 'status' => 'required'],
        ];

        // Deployment checklist
        $checklist = [
            ['task' => 'Add 8 GitLab CI/CD Variables', 'status' => 'pending', 'priority' => 'high'],
            ['task' => 'Create MySQL Database in Hostinger', 'status' => 'completed', 'priority' => 'high'],
            ['task' => 'Set Document Root to /public_html/public', 'status' => 'pending', 'priority' => 'high'],
            ['task' => 'Push Code to GitLab (Triggers Deployment)', 'status' => 'completed', 'priority' => 'high'],
            ['task' => 'Monitor GitLab CI/CD Pipeline', 'status' => 'in_progress', 'priority' => 'medium'],
            ['task' => 'Run Database Migrations via SSH', 'status' => 'pending', 'priority' => 'high'],
            ['task' => 'Test Live Site at aitechhub.store', 'status' => 'pending', 'priority' => 'medium'],
            ['task' => 'Verify Search Functionality', 'status' => 'pending', 'priority' => 'low'],
            ['task' => 'Test Newsletter Signup', 'status' => 'pending', 'priority' => 'low'],
            ['task' => 'Configure SSL Certificate (HTTPS)', 'status' => 'pending', 'priority' => 'medium'],
        ];

        // Documentation links
        $documentation = [
            ['title' => 'Enhanced Landing Page Features', 'file' => 'ENHANCED_LANDING_PAGE.md', 'size' => '~15 pages'],
            ['title' => 'GitLab Environment Variables Setup', 'file' => 'GITLAB_ENV_VARIABLES_SETUP.md', 'size' => '~8 pages'],
            ['title' => 'Hostinger Quick Start Guide', 'file' => 'HOSTINGER_QUICK_START.md', 'size' => '~10 pages'],
            ['title' => 'Hostinger FTP Setup', 'file' => 'HOSTINGER_FTP_SETUP.md', 'size' => '~5 pages'],
            ['title' => 'GitLab Variables Setup Guide', 'file' => 'GITLAB_VARIABLES_SETUP.md', 'size' => '~6 pages'],
            ['title' => 'Environment Setup Instructions', 'file' => 'ENV_SETUP_INSTRUCTIONS.md', 'size' => '~8 pages'],
        ];

        return view('admin.deployment.index', compact(
            'deploymentInfo',
            'gitlabVariables',
            'checklist',
            'documentation'
        ));
    }
}
