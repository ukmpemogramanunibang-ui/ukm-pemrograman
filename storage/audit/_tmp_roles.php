foreach ([1,2,3,4,5,6,7,8] as $lvl) {
    $u = App\Models\User::where('level_id', $lvl)->first();
    if (!$u) { echo "level $lvl: (kosong)\n"; continue; }
    echo "level $lvl: " . $u->name . " -> " . $u->getDashboardRoute() . "\n";
}
