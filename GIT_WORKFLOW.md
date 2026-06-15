# Safe Git Workflow

Use a separate branch for every task. Do not develop directly on `main`.

## Start a task

```bash
git switch main
git pull --rebase origin main
git switch -c amna/short-task-name
```

Each teammate should use a unique branch prefix, such as `amna/` or
`teammate/`.

## Save and publish work

```bash
git add <changed-files>
git commit -m "Describe the completed change"
git fetch origin
git rebase origin/main
git push -u origin HEAD
```

Open a pull request into `main`. Merge only after reviewing the changed files
and confirming that tests pass.

## Resolve a rebase conflict

```bash
git status
# Edit the conflicted files and keep the intended changes from both sides.
git add <resolved-files>
git rebase --continue
```

To safely cancel the operation:

```bash
git rebase --abort
```

Never use `git push --force` on `main`. Avoid committing generated files,
personal device IDs, editor settings, Apple development team IDs, or secrets.

Coordinate before editing shared files such as `routes/web.php` and
`resources/views/visitor-app.blade.php`. Most Flutter work should stay under
`tripolizoo1/`.
