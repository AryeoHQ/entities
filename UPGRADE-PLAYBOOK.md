# Upgrade `aryeo/tooling-laravel` — Prompt Playbook

Use the prompts below **in order**, one per step. Each prompt is self-contained — paste it as-is into a new conversation or continue in the same one.

> **Prerequisites:** The `entities` project must be available in the workspace as a reference (read-only).

---

## Step 1 — Snapshot current tooling state

```text
Review the currently installed version of `aryeo/tooling-laravel` in this project. Understand:
- What version is installed
- How it's configured (testbench.yaml, phpunit.xml, composer.json, any tooling config files)
- How tests are structured (directories, namespaces, base classes)
- What tooling commands are available and how they're invoked
- Other important features we are leveraging and need to onboard to any changes

Produce a thorough snapshot summary so we have a baseline before upgrading.
```

---

## Step 2 — Upgrade and align config

```text
I have upgraded `aryeo/tooling-laravel` to `0.0.27`. Compare how this project uses the tooling versus how `entities` (available in the workspace) uses it. Use `entities` as the reference implementation — it's already on `0.0.27`.

Identify every config difference and create an action plan to align this project. Then execute the plan:
- testbench.yaml (env vars: PINT_PATHS, PHPSTAN_PATHS, RECTOR_PATHS)
- phpunit.xml (schema version, source excludes for Test.php/TestCases.php)
- composer.json (exclude-from-classmap for *Test.php, *TestCases.php, /tests/)
- composer-dependency-analyser.php (create if missing, exclude test files)
- whatever else is important
```

---

## Step 3 — Create working document.

Create a markdown file of the tasks as a checklist we need to complete from our findings.

---

## Step 4 — Execute the plan
Create a #todo from the plan. Then complete each item in the #todo, marking each task as complete in the #todo and the plan document before proceeding to the next step.

After making changes, run all four verification commands and confirm they pass:
1. `./vendor/bin/testbench tooling:pint`
2. `./vendor/bin/testbench tooling:phpstan`
3. `./vendor/bin/testbench tooling:rector --dry-run`
4. `./vendor/bin/phpunit`

---


## Step 5 — Adopt co-located tests

```text
Adopt the co-located test strategy used by `entities` and `aryeo/tooling-laravel`. Tests should live alongside the source files they cover in `src/`, not in a separate `tests/` tree.

For each test file currently in `tests/`:
1. Move it to the corresponding location in `src/`
2. Update its namespace to match the source namespace (drop the `Tests\` prefix)
3. Keep the `use Tests\TestCase;` import (TestCase stays in tests/)

Then update phpunit.xml:
- Change the test suite directory from `./tests` to `./src`
- Rename the test suite from `Feature` to `All`

Clean up empty directories under `tests/` (but keep `tests/TestCase.php`).

After all changes, run verification:
1. `./vendor/bin/testbench tooling:pint`
2. `./vendor/bin/testbench tooling:phpstan`
3. `./vendor/bin/testbench tooling:rector --dry-run`
4. `./vendor/bin/phpunit`
```
