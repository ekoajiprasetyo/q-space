<?php
/**
 * Q-Space Maintenance Tool v12 (Auto Migration)
 * Password Protected
 */
$password = 'buka-qspace';
// ... (Auto Detect Path Logic same as v11) ...
// START AUTO-DETECTION
$possible_core_paths = [
    __DIR__ . '/../../q-space-core',
    __DIR__ . '/../q-space-core',
    __DIR__ . '/q-space-core', 
    '/home/englishh/q-space-core',
    $_SERVER['DOCUMENT_ROOT'] . '/../q-space-core'
];

$core_path = null;
foreach ($possible_core_paths as $path) {
    if (file_exists($path . '/bootstrap/app.php') && file_exists($path . '/vendor/autoload.php')) {
        $core_path = realpath($path);
        break;
    }
}
// END AUTO-DETECTION

session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    if (isset($_POST['password']) && $_POST['password'] === $password) { $_SESSION['authenticated'] = true; header('Location: ' . $_SERVER['PHP_SELF']); exit; }
    echo '<form method="post"><input type="password" name="password" placeholder="Password"><button>Login</button></form>'; exit;
}

if (!$core_path) { die("<h1>CRITICAL ERROR: Core folder not found.</h1>"); }

$debug_log = "Core Path Detected: " . $core_path . "\n";
$laravel_loaded = false;
$socialite_url = '';

try {
    require $core_path . '/vendor/autoload.php';
    $app = require_once $core_path . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
    $laravel_loaded = true;
} catch (\Throwable $e) { $debug_log .= "Laravel Boot Warning: " . $e->getMessage() . "\n"; }

// Action: Migrate
if (($_POST['action'] ?? '') === 'migrate') {
    if ($laravel_loaded) {
        try {
            Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $debug_log .= "Migration Output:\n" . Illuminate\Support\Facades\Artisan::output() . "\n";
        } catch (\Exception $e) {
            $debug_log .= "Migration Failed: " . $e->getMessage() . "\n";
        }
    }
}

// Action: Update .env
if (($_POST['action'] ?? '') === 'update_google_env') {
    $env_file = $core_path . '/.env';
    $content = file_exists($env_file) ? file_get_contents($env_file) : '';
    $keys = [
        'APP_URL' => trim($_POST['app_url']),
        'GOOGLE_CLIENT_ID' => trim($_POST['google_client_id']),
        'GOOGLE_CLIENT_SECRET' => trim($_POST['google_client_secret']),
        'GOOGLE_REDIRECT_URI' => trim($_POST['google_redirect_uri']),
    ];
    foreach ($keys as $key => $val) {
        $pattern = "/^{$key}=.*/m";
        $line = "{$key}={$val}";
        if (preg_match($pattern, $content)) $content = preg_replace($pattern, $line, $content);
        else $content .= PHP_EOL . $line;
    }
    file_put_contents($env_file, $content);
    array_map('unlink', glob($core_path . '/bootstrap/cache/*.php'));
    $debug_log .= "Settings updated & Cache Cleared.\n";
}

// Read Env
$current = [];
if (file_exists($core_path . '/.env')) {
    foreach (file($core_path . '/.env') as $line) {
        $parts = explode('=', trim($line), 2);
        if (count($parts) === 2) $current[$parts[0]] = $parts[1];
    }
}
?>
<!DOCTYPE html>
<html>
<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-5 bg-light">
    <div class="card shadow">
        <div class="card-body">
            <h3>Q-Space Doctor v12 (Auto Migrate)</h3>
            <div class="alert alert-warning">
                <strong>Requirement:</strong> Please run Migration to add <code>google_email</code> column to database.
            </div>
            
             <form method="post" class="mb-3">
                <input type="hidden" name="action" value="migrate">
                <button class="btn btn-warning w-100 fw-bold">RUN DATABASE MIGRATION</button>
            </form>
            <hr>
             <form method="post" class="mb-3">
                <input type="hidden" name="action" value="update_google_env">
                 <div class="row g-2">
                    <div class="col-md-6"><input type="text" name="app_url" class="form-control" placeholder="APP_URL" value="<?= $current['APP_URL'] ?? '' ?>"></div>
                    <div class="col-md-6"><input type="text" name="google_redirect_uri" class="form-control" placeholder="Redirect URI" value="<?= $current['GOOGLE_REDIRECT_URI'] ?? '' ?>"></div>
                    <div class="col-md-6"><input type="text" name="google_client_id" class="form-control" placeholder="Client ID" value="<?= $current['GOOGLE_CLIENT_ID'] ?? '' ?>"></div>
                    <div class="col-md-6"><input type="text" name="google_client_secret" class="form-control" placeholder="Client Secret" value="<?= $current['GOOGLE_CLIENT_SECRET'] ?? '' ?>"></div>
                    <div class="col-12"><button class="btn btn-primary w-100">Update .env & Clear Cache</button></div>
                </div>
            </form>
            <pre class="bg-dark text-white p-3 rounded" style="white-space: pre-wrap; word-break: break-all;"><?= htmlspecialchars($debug_log) ?></pre>
        </div>
    </div>
</body>
</html>
