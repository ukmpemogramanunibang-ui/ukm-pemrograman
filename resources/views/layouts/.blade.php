<form method="POST" action="{{ route('logout') }}" onsubmit="return clearExplorerTraces()">
    @csrf
    <button type="submit" class="...">Logout</button>
</form>

<script>
function clearExplorerTraces() {
    // Hapus jejak explorer di browser
    ['impersonation_token','impersonate_token','explore_token','explore_verified',
     'impersonate_active','explore_user_id','impersonator_id','role_slug'
    ].forEach(k => {
        try { localStorage.removeItem(k); } catch(e) {}
        try { sessionStorage.removeItem(k); } catch(e) {}
        // Hapus cookie
        document.cookie = k + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    });
    return true; // lanjutkan submit form
}
</script>