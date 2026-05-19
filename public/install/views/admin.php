<div class="card">
    <h2 class="card-title">Administrator Account</h2>
    <p class="card-subtitle">Create the superadmin account for managing the platform.</p>

    <form id="admin-form" method="post" action="?step=settings">
        <div class="form-group">
            <label for="admin-name">Full Name</label>
            <input type="text" id="admin-name" name="name" value="Administrator" required>
        </div>

        <div class="form-group">
            <label for="admin-email">Email Address</label>
            <input type="email" id="admin-email" name="email" placeholder="admin@example.com" required>
            <p class="input-hint">This will be your login username.</p>
        </div>

        <div class="form-group">
            <label for="admin-password">Password</label>
            <input type="password" id="admin-password" name="password" placeholder="••••••••" required minlength="12">
            <p class="input-hint">Minimum 12 characters for security.</p>
        </div>

        <div class="form-group">
            <label for="admin-password-confirm">Confirm Password</label>
            <input type="password" id="admin-password-confirm" name="password_confirm" placeholder="••••••••" required>
        </div>

        <div class="nav-buttons">
            <a href="?step=database" class="btn btn-secondary">← Back</a>
            <button type="submit" class="btn btn-primary">
                Continue →
            </button>
        </div>
    </form>
</div>