# Purging secrets from Git history

This guide explains safe steps to remove sensitive files or values (for example `.env` or API tokens) from the Git history. This operation rewrites history and requires force-pushing — coordinate with collaborators before proceeding.

IMPORTANT: do NOT run these commands unless you understand the impact. Back up your repository first.

Recommended tools
- `git-filter-repo` (recommended): https://github.com/newren/git-filter-repo
- `BFG Repo-Cleaner` (alternative): https://rtyley.github.io/bfg-repo-cleaner/

High-level steps (git-filter-repo)

1. Create a bare mirror clone (work on the mirror to avoid corrupting your working tree):

```bash
git clone --mirror https://github.com/your/repo.git repo.git
cd repo.git
```

2. Remove files (example: `.env`) from all history:

```bash
git filter-repo --invert-paths --path .env
```

3. (Optional) Remove any string patterns (tokens). Create a `replacements.txt` with patterns to replace or delete. Example format (see git-filter-repo docs):

```
literal-to-remove==>REMOVED
another-secret==>REMOVED
```

Then run:

```bash
git filter-repo --replace-text replacements.txt
```

4. Cleanup and force-push mirror to origin:

```bash
git reflog expire --expire=now --all
git gc --prune=now --aggressive
git push --force --prune origin refs/heads/*
```

5. Ask collaborators to re-clone the repository (or reset local clones). Any local branches will need to be re-created from the cleaned remote.

High-level steps (BFG)

1. Create a mirror clone (same as above):

```bash
git clone --mirror https://github.com/your/repo.git repo.git
```

2. Run BFG to delete files named `.env` or to replace passwords:

```bash
java -jar bfg.jar --delete-files .env repo.git
# or to replace text (sensitive values)
java -jar bfg.jar --replace-text passwords.txt repo.git
```

3. Cleanup and push (inside `repo.git`):

```bash
cd repo.git
git reflog expire --expire=now --all && git gc --prune=now --aggressive
git push --force
```

Caveats & checklist
- Back up the repo before changes.
- Inform all collaborators — they will need to re-clone after the rewrite.
- Rotated secrets first (change keys/tokens) so exposed values become invalid.
- After push, check CI/CD, webhooks, and deploy keys — update as needed.

If you want, run the interactive PowerShell helper `scripts/purge-history.ps1` included in this repo — it automates the mirror creation and runs `git-filter-repo` if installed. The script will prompt before destructive steps.
