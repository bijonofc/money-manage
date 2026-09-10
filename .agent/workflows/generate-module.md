---
description: Generate a full CRUD module (List + Form + Composable) using @appsbd/vue3-appsbd-ui components
---

# Generate Module

## Before anything else

Invoke `/use-appsbd-ui` to load the component reference. Use only props/events/slots documented there.

## What to generate

Given a module name and field definitions, scaffold three files:

### 1. `[Name]List.vue` — List/Table view

- Use `AbTable` for the data grid
- `AbButton` (primary) to open the create form
- `AbConfirmPopover` on each row's delete action
- `AbSkeleton` while data is loading
- `AbBadge` for status/boolean columns
- Call `useToast` on successful delete

### 2. `[Name]Form.vue` — Create / Edit form

- Wrap everything in `AbModal` with `v-model:is-modal-visible`
- Map each field to the correct Ab* input:
  | Field type     | Component         |
  |----------------|-------------------|
  | text / email   | `AbInputField`    |
  | number         | `AbNumberField`   |
  | select (single)| `AbMultiSelect` mode="single" |
  | select (multi) | `AbMultiSelect` mode="multiple" |
  | boolean/switch | `AbToggle`        |
  | date / range   | `AbDateTimePicker`|
  | radio group    | `AbFormCheck`     |
  | textarea       | `AbInputField` type="textarea" |
  | file           | `AbFileUploader`  |
- Add VeeValidate `rules` prop on required / validated fields
- `@on-submit` handler calls composable save method
- Call `modal.value.showLoader(true)` before async, `showLoader(false)` after
- Call `useToast` success/error inside the submit handler

### 3. `use[Name].js` — Composable

- `ref` for list data, single item, loading state, pagination
- `fetchList()`, `fetchOne(id)`, `save(payload)`, `remove(id)` functions
- `save` detects create vs edit by presence of `id`
- Return all refs and functions

## Output format

Ask the user for:
1. **Module name** (PascalCase, e.g. `Product`)
2. **Fields** — name, type, required?, options (for selects)
3. **API base path** (e.g. `/api/products`)
4. **Output folder** (default: `src/modules/[name]/`)

Then generate all three files in one response, each in a separate code block labeled with its file path.

## Example invocation

```
/generate-module

Module: Invoice
Fields:
- title (text, required)
- amount (number, required, min: 0)
- status (select: Draft | Sent | Paid, required)
- due_date (date, required)
- notes (textarea)

API: /api/invoices
Output: src/modules/invoices/
```
