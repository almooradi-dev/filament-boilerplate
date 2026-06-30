---
name: filament-check
description: >-
  Static analysis for Laravel Filament v4/v5 code. Scans Filament resources,
  schemas, tables, widgets, and pages for deprecated APIs, performance problems,
  security gaps, UX issues, and best-practice violations, then reports them in a
  clean grouped format with before/after fixes. Use this skill whenever the user
  asks to check, review, audit, lint, or "filacheck" Filament code; whenever they
  paste or point at Filament Resource/Schema/Table/Widget code; after an AI agent
  generates Filament code; or when migrating a project to Filament v4. Trigger it
  even when the user just says "review my Filament resource" or "is this Filament
  code up to date" without naming the rules explicitly.
---

# Filament Check

Catch common problems in Laravel Filament v4/v5 code the way a linter would —
deprecated methods, N+1 risks, missing security guards, and best-practice misses
— and present them so the developer can act on each one immediately.

This skill mirrors the rule set of the FilaCheck package (35 rules: 16 deprecation
rules plus 19 quality rules). It does not require the package to be installed; the
full rule catalog lives in `references/rules.md`.

## When to scan

Run a scan when the user wants Filament code reviewed, audited, or checked for
v4/v5 readiness, or hands over Filament code (Resources, Schemas, Forms, Tables,
Widgets, Pages, Relation Managers, custom Filters/Actions, casts, or Blade views
inside a Filament theme).

## Workflow

1. **Detect the Filament version FIRST.** This determines which rules apply, so do it
   before scanning anything. The deprecation rules describe v3 → v4/v5 changes — on a
   v3 project the old methods still work, so flagging them as broken would be wrong.
   Determine the version in this order of reliability:
   - `composer.lock` — search for `"name": "filament/filament"` and read its `version`.
     This is the *installed* version and the most reliable source.
   - `composer.json` — read the `filament/filament` constraint under `require`
     (e.g. `"^4.0"`, `"^3.2"`). Note this is the allowed range, not the exact install.
   - `vendor/filament/filament/composer.json` if present.

   Then act on what you found:
   - **v4 / v5** → run the full catalog, including all deprecation rules. This is the
     primary target.
   - **v3** → the v4 deprecation rules do **not** apply yet. Report them only as an
     optional "upgrade preview" section clearly labelled as not-yet-required, and still
     run the version-agnostic quality rules (security, performance, most best-practice/UX).
   - **Can't determine** (e.g. the user pasted a bare snippet with no project files) →
     don't guess silently. State the assumption you're making — "Assuming Filament v4;
     tell me if you're on v3" — scan as v4, and note it in the report header. Ask which
     version only if the distinction would flip key findings and the user is clearly mid-task.

   Show the detected version in the report header so the developer knows what the scan
   was calibrated against.

2. **Locate the code.** If files are provided or on disk, scan them. In a project,
   focus on `app/Filament/**`, plus any custom widgets, pages, enums cast in models,
   and Blade files under a Filament theme. If the user pasted a snippet, scan only
   that — don't ask for a whole repo.

3. **Load the rule catalog.** Read `references/rules.md` before scanning. It holds
   all 35 rules with their category, severity, detection pattern, and the exact
   before → after fix. Do not scan from memory — match against the catalog so every
   finding is accurate and the suggested fix is correct.

4. **Match the version-appropriate rules against every file.** For each rule, look for
   its pattern. Record the file, the line number (or a tight code excerpt if line numbers
   aren't available), and which rule fired. A single file can trigger several rules; report
   each occurrence.

5. **Report using the format below.** Group by severity, lead with a summary line
   (and the detected version), and give each finding its fix. Keep it scannable.

6. **Offer to fix.** After the report, offer to apply the auto-fixable changes
   (rules marked auto-fixable in the catalog are safe mechanical rewrites). Only
   edit files the user has given you write access to, and never auto-fix a rule that
   needs human judgement — flag those for review instead.

## Severity levels

- **Deprecation** — code that breaks or warns in Filament v4/v5. Highest priority.
- **Security** — missing guards that expose the app (e.g. unrestricted uploads).
- **Performance** — patterns that cause N+1 queries, excessive polling, or slow tables.
- **Best Practice / UX** — code that works but should be improved for maintainability
  or user experience.

## Report format

ALWAYS use this structure. Skip any section that has no findings (e.g. omit
"Security" entirely if nothing fired). If nothing fires at all, say so clearly and
congratulate the clean bill of health rather than padding the report.

```
# 🔍 Filament Check — <N> issue(s) in <M> file(s)

**Detected: Filament <version>**  ·  <X> deprecation · <Y> security · <Z> performance · <W> best-practice

---

## ⛔ Deprecations (<count>)

### `rule-name`
`path/to/File.php` · line <n>
> Short plain-language description of what's wrong.

```diff
- ->reactive()
+ ->live()
```
🔧 Auto-fixable   (omit this line for rules that need manual review)

---

## 🔐 Security (<count>)
... same per-finding layout ...

## 🐢 Performance (<count>)
... same per-finding layout ...

## 💡 Best Practices & UX (<count>)
... same per-finding layout ...

---

### Next step
<one sentence offering to apply the N auto-fixable fixes, or noting what needs manual review>
```

Rules for the report:
- Lead with the headline count so the developer sees scope at a glance.
- Order sections by severity: Deprecations → Security → Performance → Best Practices/UX.
- Within a section, group repeated instances of the same rule together but show each
  file:line so nothing is hidden.
- Every finding gets a `diff` block showing the concrete before → after. This is the
  payoff — keep the minus/plus lines tight and real, drawn from the user's actual code
  where possible, not generic placeholders.
- Mark auto-fixable findings with the 🔧 line so the developer knows what's safe to
  batch-apply.
- Keep prose minimal. The value is in precise file:line + fix, not commentary.

## Notes on accuracy

- Some rules are heuristic (e.g. "table has more than 10 columns", "form has more than
  8 ungrouped fields"). When a rule depends on a threshold, state the count you observed
  so the developer can judge.
- A few rules legitimately have exceptions (a `FileUpload` with no `acceptedFileTypes()`
  may be intentional). Report them as suggestions, not errors, and let the developer decide.
- If you're unsure whether something matches, surface it as a low-confidence note rather
  than dropping it — a missed deprecation is worse than a flagged maybe.