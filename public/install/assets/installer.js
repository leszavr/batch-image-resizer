/**
 * Image Processing Platform - Installer JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check requirements on requirements page
    const requirementsList = document.getElementById('requirements-list');
    if (requirementsList) {
        checkRequirements();
    }
    
    // Database test button
    const testDbBtn = document.getElementById('test-database');
    if (testDbBtn) {
        testDbBtn.addEventListener('click', testDatabase);
    }
    
    // Admin form validation
    const adminForm = document.getElementById('admin-form');
    if (adminForm) {
        adminForm.addEventListener('submit', validateAdminForm);
    }
    
    // Finalize installation
    const finalizeBtn = document.getElementById('finalize-installation');
    if (finalizeBtn) {
        finalizeBtn.addEventListener('click', finalizeInstallation);
    }
});

/**
 * Check system requirements via AJAX
 */
async function checkRequirements() {
    const list = document.getElementById('requirements-list');
    const nextBtn = document.getElementById('next-btn');
    
    try {
        const response = await fetch('?action=api&endpoint=check-requirements');
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Failed to check requirements');
        }
        
        let allPassed = true;
        list.innerHTML = '';
        
        Object.entries(result.data).forEach(([key, req]) => {
            const item = document.createElement('div');
            item.className = `requirement-item ${req.passed ? 'passed' : (key === 'composer' ? 'warning' : 'failed')}`;
            
            const status = req.passed ? '✓' : (key === 'composer' ? '⊘' : '✗');
            
            item.innerHTML = `
                <span class="requirement-name">${status} ${escapeHtml(req.name)}</span>
                <span class="requirement-value">${escapeHtml(req.current)}</span>
            `;
            
            list.appendChild(item);
            
            if (!req.passed && key !== 'composer') {
                allPassed = false;
            }
        });
        
        if (allPassed) {
            nextBtn.removeAttribute('disabled');
            nextBtn.innerHTML = 'Continue →';
            nextBtn.onclick = function() {
                window.location.href = '?step=database';
            };
            nextBtn.style.cursor = 'pointer';
        } else {
            nextBtn.innerHTML = 'Fix Requirements to Continue';
            nextBtn.onclick = null;
            nextBtn.style.cursor = 'not-allowed';
        }
        
    } catch (error) {
        list.innerHTML = `<div class="alert alert-error">${escapeHtml(error.message)}</div>`;
    }
}

/**
 * Test database connection
 */
async function testDatabase() {
    const btn = document.getElementById('test-database');
    const result = document.getElementById('test-result');
    
    const host = document.getElementById('db-host').value;
    const port = document.getElementById('db-port').value;
    const database = document.getElementById('db-database').value;
    const username = document.getElementById('db-username').value;
    const password = document.getElementById('db-password').value;
    
    if (!host || !username) {
        showResult(result, 'Host and username are required', false);
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Testing...';
    
    try {
        const response = await fetch('?action=api&endpoint=test-database', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ host, port, database, username, password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showResult(result, `Connected! MySQL ${data.version}`, true);
            const nextBtn = document.getElementById('next-btn');
            nextBtn.removeAttribute('disabled');
            nextBtn.onclick = function() {
                window.location.href = '?step=admin';
            };
            nextBtn.style.cursor = 'pointer';
        } else {
            showResult(result, data.error, false);
        }
        
    } catch (error) {
        showResult(result, error.message, false);
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Test Connection';
    }
}

function showResult(element, message, success) {
    element.className = `test-result show ${success ? 'success' : 'error'}`;
    element.textContent = message;
}

/**
 * Validate admin form
 */
function validateAdminForm(e) {
    const name = document.getElementById('admin-name').value.trim();
    const email = document.getElementById('admin-email').value.trim();
    const password = document.getElementById('admin-password').value;
    const confirm = document.getElementById('admin-password-confirm').value;
    
    if (!name || !email || !password) {
        e.preventDefault();
        alert('All fields are required');
        return false;
    }
    
    if (!email.includes('@')) {
        e.preventDefault();
        alert('Please enter a valid email address');
        return false;
    }
    
    if (password.length < 12) {
        e.preventDefault();
        alert('Password must be at least 12 characters long');
        return false;
    }
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match');
        return false;
    }
    
    return true;
}

/**
 * Finalize installation
 */
async function finalizeInstallation() {
    const btn = document.getElementById('finalize-installation');
    const progress = document.getElementById('install-progress');
    const progressFill = progress.querySelector('.progress-fill');
    const progressText = progress.querySelector('.progress-text');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Installing...';
    progress.classList.remove('hidden');
    
    const steps = [
        { name: 'Creating configuration...', progress: 20 },
        { name: 'Setting up database...', progress: 50 },
        { name: 'Creating admin account...', progress: 80 },
        { name: 'Finalizing...', progress: 100 }
    ];
    
    for (const step of steps) {
        progressFill.style.width = step.progress + '%';
        progressText.textContent = step.name;
        await sleep(800);
    }
    
    try {
        const response = await fetch('?action=api&endpoint=finalize', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = '?step=complete';
        } else {
            throw new Error(data.error);
        }
        
    } catch (error) {
        alert('Installation failed: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = 'Retry Installation';
    }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}