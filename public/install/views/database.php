<div class="card">
    <h2 class="card-title">Database Configuration</h2>
    <p class="card-subtitle">Enter your MySQL/MariaDB database credentials.</p>

    <form method="post" action="?step=admin">
        <div class="form-row">
            <div class="form-group">
                <label for="db-host">Database Host</label>
                <input type="text" id="db-host" name="host" value="localhost" required>
            </div>
            <div class="form-group">
                <label for="db-port">Port</label>
                <input type="number" id="db-port" name="port" value="3306" required>
            </div>
        </div>

        <div class="form-group">
            <label for="db-database">Database Name</label>
            <input type="text" id="db-database" name="database" placeholder="ipp" required>
            <p class="input-hint">The database will be created if it doesn't exist.</p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="db-username">Username</label>
                <input type="text" id="db-username" name="username" placeholder="root" required>
            </div>
            <div class="form-group">
                <label for="db-password">Password</label>
                <input type="password" id="db-password" name="password" placeholder="••••••••">
            </div>
        </div>

        <button type="button" id="test-database" class="btn btn-secondary">
            Test Connection
        </button>

        <div id="test-result" class="test-result"></div>

    <div class="nav-buttons">
        <a href="?step=requirements" class="btn btn-secondary">← Back</a>
        <button type="submit" class="btn btn-primary" id="next-btn" disabled>
            Continue →
        </button>
    </div>
    </form>
</div>