---
name: settings-form
description: Use when building a settings or preferences page with AbSettingsForm
---

# Generate a Settings Form

## Before anything else

Read `node_modules/@appsbd/vue3-appsbd-ui/.ai/ai_ref_AbSettingsForm.md` for the full API.

## What AbSettingsForm does

- Wraps fields in a VeeValidate `<Form>` with a built-in submit/loading state.
- When nested inside an `AbWizardStep`, auto-registers as the step's validator — returning `false` from `onSubmit` blocks the wizard from advancing.
- Standalone use (outside a wizard) works as a plain validated form.

## Supported field components

| Field type | Component |
|---|---|
| text / email / password | `<ab-input-field>` |
| number | `<ab-number-field>` |
| select (single) | `<ab-multi-select mode="single">` |
| select (multi) | `<ab-multi-select mode="multiple">` |
| boolean / switch | `<ab-toggle>` |
| date | `<ab-date-time-picker>` |
| checkbox / radio group | `<ab-form-check>` |
| textarea | `<ab-input-field type="textarea">` |
| file / avatar | `<ab-file-uploader>` |
| tags | `<ab-input-tag>` |
| color | `<ab-color-picker>` |

## Rules

- Import `AbSettingsForm` explicitly — it is **not** globally registered by default unless registered at app level.
- Pass the save handler to `:on-submit`. The component manages the loading state automatically — do not add a separate loading ref.
- Return `false` (or throw) from `onSubmit` to block wizard progression or keep the form in error state.
- Wrap each field in `<ab-field>` only when you need a standalone label+error wrapper outside a component that has its own label prop.
- All `Ab*` form inputs must receive a `name` prop for VeeValidate to track them.

## Standalone settings page

```vue
<template>
  <ab-settings-form :on-submit="save">
    <ab-input-field v-model="form.name"  name="name"  label="Display name"  rules="required" />
    <ab-input-field v-model="form.email" name="email" label="Email"          rules="required|email" />
    <ab-toggle      v-model="form.notifications" name="notifications" title="Email notifications" />
    <ab-multi-select
      v-model="form.language"
      name="language"
      label="Language"
      :options="languageOptions"
      label-key="title"
      value-key="val"
    />
  </ab-settings-form>
</template>

<script setup>
import { reactive } from "vue";
import { AbSettingsForm } from "@appsbd/vue3-appsbd-ui";

const form = reactive({ name: "", email: "", notifications: false, language: "en" });

async function save(values) {
  await api.save(values);
}
</script>
```
