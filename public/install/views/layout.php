<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Image Processing Platform</title>
    <link rel="stylesheet" href="assets/installer.css">
</head>
<body>
    <div class="installer-container">
        <header class="installer-header">
            <div class="logo">
                <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    <path d="M17.5 14v6M14.5 17h6" stroke-linecap="round"/>
                </svg>
                <span class="logo-text">IPP Installer</span>
            </div>
            <div class="version">v1.0.0</div>
        </header>

        <main class="installer-main">
            <aside class="installer-sidebar">
                <nav class="step-nav">
                    <div class="step-item <?php echo $currentStep >= 1 ? 'active' : ''; ?> <?php echo in_array(1, $_SESSION['installer']['passed_steps'] ?? []) ? 'completed' : ''; ?>">
                        <span class="step-number">1</span>
                        <span class="step-label">Welcome</span>
                    </div>
                    <div class="step-item <?php echo $currentStep >= 2 ? 'active' : ''; ?> <?php echo in_array(2, $_SESSION['installer']['passed_steps'] ?? []) ? 'completed' : ''; ?>">
                        <span class="step-number">2</span>
                        <span class="step-label">Requirements</span>
                    </div>
                    <div class="step-item <?php echo $currentStep >= 3 ? 'active' : ''; ?> <?php echo in_array(3, $_SESSION['installer']['passed_steps'] ?? []) ? 'completed' : ''; ?>">
                        <span class="step-number">3</span>
                        <span class="step-label">Database</span>
                    </div>
                    <div class="step-item <?php echo $currentStep >= 4 ? 'active' : ''; ?> <?php echo in_array(4, $_SESSION['installer']['passed_steps'] ?? []) ? 'completed' : ''; ?>">
                        <span class="step-number">4</span>
                        <span class="step-label">Admin Account</span>
                    </div>
                    <div class="step-item <?php echo $currentStep >= 5 ? 'active' : ''; ?> <?php echo in_array(5, $_SESSION['installer']['passed_steps'] ?? []) ? 'completed' : ''; ?>">
                        <span class="step-number">5</span>
                        <span class="step-label">Settings</span>
                    </div>
                    <div class="step-item <?php echo $currentStep >= 6 ? 'active' : ''; ?>">
                        <span class="step-number">6</span>
                        <span class="step-label">Complete</span>
                    </div>
                </nav>
            </aside>

            <section class="installer-content">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php include $stepFile; ?>
            </section>
        </main>

        <footer class="installer-footer">
            <p>&copy; <?php echo date('Y'); ?> Image Processing Platform. All rights reserved.</p>
        </footer>
    </div>

    <script src="assets/installer.js"></script>
</body>
</html>