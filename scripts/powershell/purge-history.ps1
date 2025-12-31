<#
Interactive helper to purge sensitive files/strings from Git history using git-filter-repo.
This script will:
 - Create a bare mirror clone of the current repo
 - Run git-filter-repo to remove specified paths or replace text
 - Run git gc and push --force

USAGE: run from the repository root as an administrator (it will create a mirror folder next to this repo)

WARNING: This rewrites history. All collaborators must re-clone after the operation.
#>

param(
    [string]$RepoUrl,
    [string[]]$PathsToDelete = @('.env'),
    [string]$ReplaceFile,
    [switch]$AutoConfirm
)

if (-not $RepoUrl) {
    # try to obtain origin
    $RepoUrl = git config --get remote.origin.url
}

if (-not $RepoUrl) {
    Write-Error "Cannot determine repo URL. Provide -RepoUrl explicitly."
    exit 1
}

$mirror = (Split-Path -Path (Get-Location) -Leaf) + ".git.mirror"
Write-Host "Mirror path: $mirror"
if (Test-Path $mirror) { Write-Host "Mirror already exists - will remove it if you confirm." }

$confirm = $null
if (-not $AutoConfirm) {
    $confirm = Read-Host "Proceed to create mirror '$mirror' and rewrite history? Type 'YES' to continue"
    if ($confirm -ne 'YES') { Write-Host 'Aborting.'; exit 0 }
} else {
    Write-Host "AutoConfirm enabled - proceeding without interactive prompt."
}

if (Test-Path $mirror) {
    if ($AutoConfirm) {
        Write-Host "Removing existing mirror at $mirror"
        Remove-Item -Recurse -Force $mirror
    } else {
        Write-Error "Mirror path already exists: $mirror. Remove it manually or run with -AutoConfirm to allow removal."
        exit 1
    }
}
git clone --mirror $RepoUrl $mirror
Set-Location $mirror

# Delete paths
foreach ($p in $PathsToDelete) {
    Write-Host "Removing path from history: $p"
    # use python -m git_filter_repo to avoid relying on git subcommand installation
    try {
        & python -m git_filter_repo --version > $null 2>&1
    } catch {
        Write-Error "git-filter-repo Python package not available. Install with: python -m pip install --user git-filter-repo"
        exit 1
    }
    & python -m git_filter_repo --invert-paths --path $p
}

if ($ReplaceFile) {
    Write-Host "Applying replacements from $ReplaceFile"
    & python -m git_filter_repo --replace-text $ReplaceFile
}

Write-Host "Cleaning up and forcing push..."
git reflog expire --expire=now --all
git gc --prune=now --aggressive
git push --force --mirror origin

Write-Host "Purge complete. Inform collaborators to re-clone the repository."
