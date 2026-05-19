<div class="card">
    <h2 class="card-title">Application Settings</h2>
    <p class="card-subtitle">Configure your platform settings.</p>

    <form id="settings-form" method="post" action="?step=complete">
        <div class="form-group">
            <label for="app-name">Application Name</label>
            <input type="text" id="app-name" name="app_name" value="Image Processing Platform" required>
        </div>

        <div class="form-group">
            <label for="app-url">Application URL</label>
            <input type="url" id="app-url" name="app_url" placeholder="https://your-domain.com" required>
            <p class="input-hint">Full URL without trailing slash.</p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="app-locale">Default Language</label>
                <select id="app-locale" name="app_locale">
                    <option value="en">English</option>
                    <option value="ru">Русский</option>
                    <option value="de">Deutsch</option>
                    <option value="fr">Français</option>
                    <option value="es">Español</option>
                </select>
            </div>
            <div class="form-group">
                <label for="app-timezone">Timezone</label>
                <select id="app-timezone" name="app_timezone">
                    <option value="UTC">UTC</option>
                    <option value="Europe/Moscow">Europe/Moscow</option>
                    <option value="Europe/London">Europe/London</option>
                    <option value="America/New_York">America/New_York</option>
                    <option value="America/Los_Angeles">America/Los_Angeles</option>
                    <option value="Asia/Tokyo">Asia/Tokyo</option>
                    <option value="Asia/Shanghai">Asia/Shanghai</option>
                    <option value="Australia/Sydney">Australia/Sydney</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="admin-email">System Email</label>
            <input type="email" id="system-email" name="admin_email" placeholder="noreply@example.com" required>
            <p class="input-hint">Used for system notifications.</p>
        </div>

        <div id="install-progress" class="progress-container hidden">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 0%"></div>
            </div>
            <div class="progress-text">Initializing...</div>
        </div>

        <div class="nav-buttons">
            <a href="?step=admin" class="btn btn-secondary">← Back</a>
            <button type="button" id="finalize-installation" class="btn btn-primary">
                Install Platform →
            </button>
        </div>
    </form>
</div>