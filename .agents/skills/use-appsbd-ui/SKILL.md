---
name: use-appsbd-ui
description: Use when generating code that uses @appsbd/vue3-appsbd-ui components or APIs
---

# Using @appsbd/vue3-appsbd-ui

## Rules

- Use `Ab*` components before reaching for any external UI library.
- Never guess props, events, or slots — always read the reference file first.
- Globally registered components need no import. All others: `import { X } from "@appsbd/vue3-appsbd-ui"`.

## Workflow

1. Read `node_modules/@appsbd/vue3-appsbd-ui/AI_REF.md` to find available components and their reference file paths.
2. Read `node_modules/@appsbd/vue3-appsbd-ui/.ai/ai_ref_[ComponentName].md` for the exact props / events / slots before writing any code.
3. Generate code using only the documented API from that file.

## Quick API (no file read needed)

```js
// Toast
import { useToast } from "@appsbd/vue3-appsbd-ui";
const { toast } = useToast();
toast.success("Done"); // .error / .warning / .info

// Alert
import { useAlert } from "@appsbd/vue3-appsbd-ui";
const { alert } = useAlert();
alert.confirm("Sure?", "", { onConfirm: async () => {} });

// Theme
import { useTheme } from "@appsbd/vue3-appsbd-ui";
const { toggleTheme } = useTheme();
```

```html
<!-- v-tooltip (global directive) -->
<ab-button v-tooltip="'Helper text'">Save</ab-button>
```
