# Filament Check — Rule Catalog (35 rules)

Match code against every rule below. Each entry gives the **detection pattern**
(what to look for), the **fix** (before → after), and whether it is **auto-fixable**
(a safe mechanical rewrite) or needs human review.

Severity key: ⛔ Deprecation · 🔐 Security · 🐢 Performance · 💡 Best Practice/UX

---

## ⛔ Deprecation rules (16)

These break or emit deprecation warnings in Filament v4/v5. Highest priority.

**Version note:** these rules describe v3 → v4/v5 changes. Apply them as errors only
when the project is on **v4/v5**. On a **v3** project the old methods still work — report
these as an optional upgrade preview, not as required fixes. (See the version-detection
step in SKILL.md.)

### `deprecated-reactive`
Detect: a call to `->reactive()` on a form field.
Fix: `->reactive()` → `->live()`
Auto-fixable: yes.

### `deprecated-action-form`
Detect: `->form([...])` called on an Action (not on a resource/page form).
Fix: `->form([...])` → `->schema([...])`
Auto-fixable: yes (when clearly on an Action).

### `deprecated-filter-form`
Detect: `->form([...])` called on a table `Filter`.
Fix: `Filter->form([...])` → `Filter->schema([...])`
Auto-fixable: yes (when clearly on a Filter).

### `deprecated-placeholder`
Detect: `Placeholder::make(...)` used in a schema.
Fix: `Placeholder::make('x')` → `TextEntry::make('x')->state(...)`
Auto-fixable: no — the value source moves into `->state()`, needs human judgement.

### `deprecated-mutate-form-data-using`
Detect: `->mutateFormDataUsing(...)`.
Fix: `->mutateFormDataUsing(...)` → `->mutateDataUsing(...)`
Auto-fixable: yes.

### `deprecated-empty-label`
Detect: `->label('')` (empty-string label) used to hide a label.
Fix: `->label('')` → `->hiddenLabel()`
Auto-fixable: yes.

### `deprecated-forms-get`
Detect: `use Filament\Forms\Get;`
Fix: → `use Filament\Schemas\Components\Utilities\Get;`
Auto-fixable: yes (import swap).

### `deprecated-forms-set`
Detect: `use Filament\Forms\Set;`
Fix: → `use Filament\Schemas\Components\Utilities\Set;`
Auto-fixable: yes (import swap).

### `deprecated-image-column-size`
Detect: `->size(40)` called on an `ImageColumn`.
Fix: `->size(40)` → `->imageSize(40)`
Auto-fixable: yes (when clearly on an ImageColumn).

### `deprecated-view-property`
Detect: `public $view = '...';` on a page/component class.
Fix: → `protected string $view = '...';`
Auto-fixable: yes.

### `action-in-bulk-action-group`
Detect: a plain `Action::make(...)` placed inside a bulk-action group / toolbar
where a bulk action belongs.
Fix: `Action::make('export')` → `BulkAction::make('export')`
Auto-fixable: no — confirm the action is genuinely a bulk action first.

### `wrong-tab-namespace`
Detect: `use Filament\Resources\Pages\...\Tab;` (Tab imported from the Pages namespace).
Fix: → `use Filament\Schemas\Components\Tabs\Tab;`
Auto-fixable: yes (import swap).

### `deprecated-bulk-actions`
Detect: `->bulkActions([...])` on a table.
Fix: `->bulkActions([...])` → `->toolbarActions([...])`
Auto-fixable: yes.

### `deprecated-url-parameters`
Detect: use of `tableFilters` or `tableSearch` URL/query parameters.
Fix: `tableFilters` → `filters`, `tableSearch` → `search`
Auto-fixable: yes.

### `deprecated-test-methods`
Detect: `->setActionData(...)` in tests.
Fix: `->setActionData(...)` → `->setData(...)`
Auto-fixable: yes.

### `deprecated-image-editor-aspect-ratios`
Detect: `->imageEditorAspectRatios([...])`.
Fix: → `->imageEditorAspectRatioOptions([...])`
Auto-fixable: yes.

---

## 🐢 Performance rules (5)

### `too-many-columns`
Detect: a table `->columns([...])` with more than 10 columns.
Why: wide tables hurt load time and readability.
Fix: reduce visible columns; move extras behind `->toggleable(isToggledHiddenByDefault: true)`.
Report the observed column count. Auto-fixable: no.

### `large-option-list-searchable`
Detect: a `Select`/`CheckboxList`/`Radio` with 10+ hard-coded options and no `->searchable()`.
Fix: add `->searchable()`.
Auto-fixable: no (suggestion).

### `heavy-closure-in-format-state`
Detect: a database query (Eloquent/DB call, `::find`, `->where(...)->get()`, etc.)
inside a `formatStateUsing(fn (...) => ...)` closure.
Why: runs once per row → N+1 queries.
Fix: eager-load the relation or precompute; don't query per row.
Auto-fixable: no.

### `stats-widget-polling-not-disabled`
Detect: a class extending `StatsOverviewWidget` without overriding the polling interval
(defaults to 5-second polling).
Fix: set `protected ?string $pollingInterval = null;` (or a sensible interval).
Auto-fixable: no (intent depends on the use case).

### `navigation-badge-not-cached`
Detect: a `getNavigationBadge()` method whose body runs a count/query without using the
`Cache` facade or the `cache()` helper.
Why: runs on every page load across navigation.
Fix: wrap the value in `cache()->remember(...)`.
Auto-fixable: no.

---

## 🔐 Security rules (1)

### `file-upload-missing-accepted-file-types`
Detect: a `FileUpload::make(...)` with neither `->acceptedFileTypes([...])` nor `->image()`.
Why: an unrestricted upload accepts any file type — an upload-security risk.
Fix: add `->acceptedFileTypes([...])` (or `->image()` for image-only).
Auto-fixable: no (the allowed types depend on the field's purpose).

---

## 💡 UX Suggestion rules (5)

### `flat-form-overload`
Detect: a form `->schema([...])` / `->components([...])` with more than 8 fields and no
layout components (`Section`, `Grid`, `Fieldset`, `Tabs`, `Wizard`).
Fix: group fields under `Section::make()` / `Grid::make()`.
Report the observed field count. Auto-fixable: no.

### `relationship-select-not-searchable`
Detect: a `Select::make(...)->relationship(...)` without `->searchable()`.
Fix: add `->searchable()`.
Auto-fixable: no (suggestion).

### `missing-table-filters`
Detect: a table with columns that look filterable (status/enum/boolean/foreign-key columns)
but no `->filters([...])` defined.
Fix: add relevant `SelectFilter` / `TernaryFilter` entries.
Auto-fixable: no.

### `table-without-searchable-columns`
Detect: a table containing `TextColumn`s where none has `->searchable()`.
Fix: mark at least the key text columns `->searchable()`.
Auto-fixable: no (suggestion).

### `filter-missing-indicator`
Detect: a custom `Filter` that defines a `schema()` / `form()` but no `->indicateUsing(...)`
or `->indicator(...)`.
Why: active custom filters are invisible to users without an indicator.
Fix: add `->indicateUsing(fn (array $data) => ...)`.
Auto-fixable: no.

---

## 💡 Best Practice rules (8)

### `custom-theme-needed`
Detect: Tailwind CSS utility classes used in Filament Blade views while no custom Filament
theme is configured.
Fix: register a custom theme so the classes are compiled.
Auto-fixable: no.

### `unnecessary-unique-ignore-record`
Detect: `->unique(ignoreRecord: true)`.
Why: `ignoreRecord: true` is the default in Filament v4, so it's redundant.
Fix: `->unique(ignoreRecord: true)` → `->unique()`
Auto-fixable: yes.

### `string-icon-instead-of-enum`
Detect: a string icon name, e.g. `->icon('heroicon-o-pencil')`.
Fix: use the enum: `->icon(Heroicon::Pencil)`.
Auto-fixable: yes.

### `string-font-weight-instead-of-enum`
Detect: a string font weight, e.g. `->weight('bold')`.
Fix: use the enum: `->weight(FontWeight::Bold)`.
Auto-fixable: yes.

### `deprecated-notification-action-namespace`
Detect: a notification `Action` imported from the old namespace.
Fix: update to the current notification Action namespace
(`Filament\Notifications\Actions\Action`).
Auto-fixable: yes (import swap).

### `file-upload-missing-max-size`
Detect: a `FileUpload::make(...)` with no `->maxSize(...)`.
Why: without a cap, users can upload arbitrarily large files.
Fix: add `->maxSize(1024)` (KB) or an appropriate limit.
Auto-fixable: no (the limit is project-specific).

### `bulk-action-missing-deselect`
Detect: a `BulkAction::make(...)` without `->deselectRecordsAfterCompletion()`.
Why: rows stay selected after the action runs, which is confusing.
Fix: add `->deselectRecordsAfterCompletion()`.
Auto-fixable: yes.

### `enum-missing-filament-interfaces`
Detect: a PHP enum that is cast on an Eloquent model and used in Filament, but does not
implement Filament interfaces such as `HasLabel` (and optionally `HasColor`, `HasIcon`).
Fix: implement `HasLabel` (plus `HasColor` / `HasIcon` as needed) on the enum.
Auto-fixable: no.

---

## Quick index

Deprecation (16): deprecated-reactive, deprecated-action-form, deprecated-filter-form,
deprecated-placeholder, deprecated-mutate-form-data-using, deprecated-empty-label,
deprecated-forms-get, deprecated-forms-set, deprecated-image-column-size,
deprecated-view-property, action-in-bulk-action-group, wrong-tab-namespace,
deprecated-bulk-actions, deprecated-url-parameters, deprecated-test-methods,
deprecated-image-editor-aspect-ratios.

Performance (5): too-many-columns, large-option-list-searchable,
heavy-closure-in-format-state, stats-widget-polling-not-disabled,
navigation-badge-not-cached.

Security (1): file-upload-missing-accepted-file-types.

UX (5): flat-form-overload, relationship-select-not-searchable, missing-table-filters,
table-without-searchable-columns, filter-missing-indicator.

Best Practice (8): custom-theme-needed, unnecessary-unique-ignore-record,
string-icon-instead-of-enum, string-font-weight-instead-of-enum,
deprecated-notification-action-namespace, file-upload-missing-max-size,
bulk-action-missing-deselect, enum-missing-filament-interfaces.