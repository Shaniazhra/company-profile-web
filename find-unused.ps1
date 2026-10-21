<#
 find-unused.ps1
 Jalankan dari folder root project Laravel (yang ada file artisan).
 Skrip ini HANYA membuat laporan (unused-report.txt). Tidak ada file yang dihapus.
#>

$ErrorActionPreference = 'Stop'
$root = (Get-Location).Path
if (-not (Test-Path (Join-Path $root 'artisan'))) {
    Write-Host 'Jalankan dari folder root project (yang ada file artisan).' -ForegroundColor Red
    exit 1
}

$report = New-Object System.Collections.Generic.List[string]
function Add-Line([string]$text) { $script:report.Add($text); Write-Host $text }
function Add-Section([string]$title) {
    Add-Line ''
    Add-Line ('=' * 70)
    Add-Line $title
    Add-Line ('=' * 70)
}
function Rel([string]$path) { $path.Substring($root.Length).TrimStart('\') }
function Start-Count { $script:count = 0 }
function End-Count { if ($script:count -eq 0) { Add-Line '  (tidak ada temuan)' } }
function Hit([string]$text) { $script:count++; Add-Line $text }

# Semua file project kecuali vendor, node_modules, dan .git
function Get-ProjectFiles {
    $skip = 'vendor', 'node_modules', '.git', '.idea', '.vscode'
    Get-ChildItem $root -File -Force
    Get-ChildItem $root -Directory -Force |
        Where-Object { $skip -notcontains $_.Name } |
        ForEach-Object { Get-ChildItem $_.FullName -Recurse -File -Force -ErrorAction SilentlyContinue }
}

# ---------- Baca semua file kode ke memori sekali saja ----------
Write-Host 'Membaca file project...' -ForegroundColor Cyan
$scanDirs = 'app', 'routes', 'bootstrap', 'config', 'database', 'resources', 'tests', 'public'
$corpus = @{}
foreach ($d in $scanDirs) {
    $p = Join-Path $root $d
    if (-not (Test-Path $p)) { continue }
    Get-ChildItem $p -Recurse -File -Include *.php, *.js, *.css, *.json, *.html -ErrorAction SilentlyContinue |
        Where-Object { $_.FullName -notmatch '\\(build|node_modules|vendor|storage)\\' } |
        ForEach-Object { $corpus[$_.FullName] = Get-Content $_.FullName -Raw -ErrorAction SilentlyContinue }
}

# Apakah teks persis ini muncul di file lain? ($exclude = file itu sendiri, $excludeDir = folder yang diabaikan)
function Test-Text([string]$needle, [string]$exclude = '', [string]$excludeDir = '') {
    foreach ($kv in $corpus.GetEnumerator()) {
        if ($exclude -and $kv.Key -eq $exclude) { continue }
        if ($excludeDir -and $kv.Key.StartsWith($excludeDir, [StringComparison]::OrdinalIgnoreCase)) { continue }
        if ($kv.Value -and $kv.Value.Contains($needle)) { return $true }
    }
    return $false
}

# Apakah kata utuh ini (misalnya nama class) muncul di file lain?
function Test-Word([string]$word, [string]$exclude = '') {
    $rx = '\b' + [regex]::Escape($word) + '\b'
    foreach ($kv in $corpus.GetEnumerator()) {
        if ($kv.Key -eq $exclude) { continue }
        if ($kv.Value -and [regex]::IsMatch($kv.Value, $rx)) { return $true }
    }
    return $false
}

# ---------- 1. Sisa file sementara / backup ----------
Add-Section '1. SISA FILE SEMENTARA / BACKUP (cek lalu hapus kalau memang tidak perlu)'
Start-Count
Get-ProjectFiles | Where-Object {
    $_.FullName -notmatch '\\storage\\(framework|logs|app)\\' -and
    ($_.Name -match '\.(bak|orig|rej|old|tmp|swp)$' -or $_.Name -match '~$' -or
     $_.Name -match 'backup' -or $_.DirectoryName -match 'backup' -or $_.Name -match '^diff-')
} | ForEach-Object { Hit ('  ' + (Rel $_.FullName)) }
End-Count

$migDir = Join-Path $root 'database\migrations'
if (Test-Path $migDir) {
    $spaced = Get-ChildItem $migDir -File | Where-Object { $_.Name -match '\s' }
    if ($spaced) {
        Add-Line ''
        Add-Line '  Nama file migration mengandung spasi (jangan di-rename kalau sudah pernah dijalankan,'
        Add-Line '  nanti Laravel mengira itu migration baru dan menjalankannya lagi):'
        $spaced | ForEach-Object { Add-Line ('  ' + (Rel $_.FullName)) }
    }
}

# ---------- 2. Class PHP yang tidak dipanggil ----------
Add-Section '2. CLASS PHP (controller, model, middleware, helper) YANG NAMANYA TIDAK DIPANGGIL'
Start-Count
Get-ChildItem (Join-Path $root 'app') -Recurse -File -Filter *.php |
    Where-Object { $_.FullName -notmatch '\\Providers\\' -and $_.BaseName -ne 'Controller' } |
    ForEach-Object {
        $name  = $_.BaseName
        $kebab = ([regex]::Replace($name, '(?<!^)([A-Z])', '-$1')).ToLower()
        if (-not (Test-Word $name $_.FullName) -and -not (Test-Text ("x-$kebab") $_.FullName)) {
            Hit ('  ' + (Rel $_.FullName))
        }
    }
End-Count

# ---------- 3. File Blade yang tidak dipanggil ----------
Add-Section '3. FILE BLADE (VIEW / KOMPONEN) YANG TIDAK DIPANGGIL'
Start-Count
$viewsDir = Join-Path $root 'resources\views'
Get-ChildItem $viewsDir -Recurse -File -Filter *.blade.php | ForEach-Object {
    $rel  = (Rel $_.FullName).Substring('resources\views\'.Length)
    $name = ($rel -replace '\.blade\.php$', '') -replace '\\', '.'
    if ($name -match '^(errors|vendor)\.') { return }

    if ($name -like 'components.*') {
        $c = ($name.Substring('components.'.Length)) -replace '\.index$', ''
        $used = Test-Text ("x-$c") $_.FullName
    } else {
        $used = (Test-Text ("'" + $name + "'") $_.FullName) -or (Test-Text ('"' + $name + '"') $_.FullName)
    }
    if (-not $used) { Hit ('  ' + (Rel $_.FullName)) }
}
End-Count

# ---------- 4. Gambar di public/images yang tidak dipakai ----------
Add-Section '4. GAMBAR DI public\images YANG NAMANYA TIDAK DISEBUT DI KODE'
Start-Count
$imgDir = Join-Path $root 'public\images'
if (Test-Path $imgDir) {
    Get-ChildItem $imgDir -Recurse -File | ForEach-Object {
        if (-not (Test-Text $_.Name)) { Hit ('  ' + (Rel $_.FullName)) }
    }
}
End-Count
Add-Line '  (Foto yang diunggah lewat admin ada di storage dan tidak dicek di sini.)'

# ---------- 5. Route yang namanya tidak dipanggil ----------
Add-Section '5. ROUTE YANG NAMANYA TIDAK DIPANGGIL DI KODE (selain file routes)'
Start-Count
try {
    $json   = & php artisan route:list --json 2>$null
    $routes = ($json -join "`n") | ConvertFrom-Json
    foreach ($r in $routes) {
        if (-not $r.name) { continue }
        if ($r.name -match '^(ignition|sanctum|storage|debugbar|livewire|generated::)') { continue }
        if ($r.action -match 'Controllers\\Auth\\') { continue }
        $a = "'" + $r.name + "'"
        $b = '"' + $r.name + '"'
        if (-not (Test-Text $a '' (Join-Path $root 'routes')) -and -not (Test-Text $b '' (Join-Path $root 'routes'))) {
            Hit ('  {0,-12} {1,-34} {2}' -f $r.method, $r.name, $r.action)
        }
    }
} catch {
    Add-Line '  Gagal membaca route:list. Lewati bagian ini.'
}
End-Count

# ---------- Simpan laporan ----------
$out = Join-Path $root 'unused-report.txt'
$report | Set-Content -Path $out -Encoding UTF8
Write-Host ''
Write-Host "Selesai. Laporan disimpan di: $out" -ForegroundColor Green