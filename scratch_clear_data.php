<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
App\Models\StudentResult::truncate();
App\Models\DiagnosticResult::truncate();
App\Models\StudentGroup::truncate();
App\Models\GameSession::truncate();
\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "All game data cleared.\n";
