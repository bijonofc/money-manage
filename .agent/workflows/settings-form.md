---
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

- Import `AbSettingsForm` explicitly — it is **not** globally registered.
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
import { ref } from "vue";
import { AbSettingsForm, useToast } from "@appsbd/vue3-appsbd-ui";

const { toast } = useToast();

const form = ref({
  name: "",
  email: "",
  notifications: false,
  language: null,
});

const languageOptions = [
  { val: "en", title: "English" },
  { val: "bn", title: "Bengali" },
  { val: "ar", title: "Arabic" },
];

async function save(values) {
  await api.saveProfile(values);
  toast.success("Settings saved.");
}
</script>
```

## Wizard with per-step validation

```vue
<template>
  <ab-wizard @finished="onFinished">
    <ab-wizard-step title="Account">
      <ab-settings-form :on-submit="validateAccount">
        <ab-input-field v-model="form.email"    name="email"    label="Email"    rules="required|email" />
        <ab-input-field v-model="form.password" name="password" label="Password" rules="required|min:8" type="password" />
      </ab-settings-form>
    </ab-wizard-step>

    <ab-wizard-step title="Profile">
      <ab-settings-form :on-submit="validateProfile">
        <ab-input-field v-model="form.name" name="name" label="Full name" rules="required" />
        <ab-multi-select
          v-model="form.role"
          name="role"
          label="Role"
          :options="roleOptions"
          label-key="title"
          value-key="val"
          rules="required"
        />
      </ab-settings-form>
    </ab-wizard-step>

    <ab-wizard-step title="Preferences">
      <ab-settings-form>
        <ab-toggle v-model="form.emails"    name="emails"    title="Email me updates" />
        <ab-toggle v-model="form.marketing" name="marketing" title="Marketing emails" />
      </ab-settings-form>
    </ab-wizard-step>
  </ab-wizard>
</template>

<script setup>
import { ref } from "vue";
import { AbSettingsForm, useToast } from "@appsbd/vue3-appsbd-ui";

const { toast } = useToast();

const form = ref({
  email: "", password: "", name: "", role: null,
  emails: false, marketing: false,
});

const roleOptions = [
  { val: "admin",  title: "Admin" },
  { val: "editor", title: "Editor" },
  { val: "viewer", title: "Viewer" },
];

async function validateAccount(values) {
  const taken = await api.checkEmail(values.email);
  if (taken) return false; // blocks wizard from advancing to next step
}

async function validateProfile(values) {
  // return false to block, or nothing to allow
}

function onFinished() {
  toast.success("Setup complete!");
}
</script>
```

## Output format

Ask the user for:
1. **Sections / steps** — one settings form per wizard step, or a single standalone form
2. **Fields per section** — name, type, required?, options (for selects)
3. **Save / API endpoint**

Then generate the Vue file(s) in one response, each in a separate code block labeled with its file path.

## Example invocation

```
/settings-form

Page: User Profile Settings
Sections:
  - Account: email (required), password (required, min 8)
  - Preferences: language (select: English|Bengali|Arabic), email_notifications (toggle), theme (select: Light|Dark)

API: /api/user/settings
Output: src/pages/settings/ProfileSettings.vue
```
