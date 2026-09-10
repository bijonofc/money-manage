---
name: migrate-lucide-icons
description: One-time migration — replace lucide-vue-next imports with @lucide/vue after upgrading @appsbd/vue3-appsbd-ui to 1.0.11+
---

# Migrate `lucide-vue-next` → `@lucide/vue`

`@appsbd/vue3-appsbd-ui` moved its icon peer dependency from `lucide-vue-next` to `@lucide/vue` (`>=1.28.0`) starting in version `1.0.11`. `@lucide/vue` exports the same named icon components under the same names, so this is a mechanical import-source rename — no icon usage or prop needs to change.

## When to run this

Once, right after upgrading `@appsbd/vue3-appsbd-ui` to `1.0.11` or later, if the project still imports icons from `lucide-vue-next`.

## Steps

1. Check whether the project still references the old package:
   ```bash
   grep -rl "lucide-vue-next" --include="*.{vue,js,ts,jsx,tsx}" resources/js
   ```
   No matches → nothing to do, stop here.
2. Swap the dependency:
   ```bash
   npm install @lucide/vue
   npm uninstall lucide-vue-next
   ```
3. Replace the import source everywhere it's used (only the string `lucide-vue-next` changes — named imports stay identical):
   ```bash
   grep -rl "lucide-vue-next" --include="*.{vue,js,ts,jsx,tsx}" resources/js | xargs sed -i 's/lucide-vue-next/@lucide\/vue/g'
   ```
4. If `package.json` lists `lucide-vue-next` directly (not just as a transitive/peer dep), replace that entry with `"@lucide/vue": "^1.28.0"`.
5. Start the dev server once and confirm icons still render.

## Rules

- Only rewrite the import source string. Never rename icon components or change their props/usage.
- Never touch `node_modules/`, `dist/`, or other build-output directories.
