---
name: laravel-frontend
description: Frontend organization conventions for Laravel Blade projects — view structure, components, layouts, Tailwind setup, and Alpine.js patterns.
origin: project
---

# Laravel Frontend Organization

Conventions for structuring Blade views, components, and assets in a role-based Laravel application.

## When to Use

- Adding new pages or features to any role's section
- Creating reusable UI components
- Deciding where a new Blade file should live
- Extending the navigation for a role

---

## View Structure

Views are organized by **role → feature → page**. Every page lives exactly 3 levels deep under `resources/views/`.

```
resources/views/
  layouts/
    app.blade.php          ← base layout for authenticated pages
    auth.blade.php         ← centered card layout for login/register
  auth/
    login.blade.php
    register.blade.php
  admin/
    dashboard/
      index.blade.php
    reports/
      index.blade.php
      show.blade.php
  salesperson/
    dashboard/
      index.blade.php
    orders/
      index.blade.php
      create.blade.php
  inventory-manager/
    dashboard/
      index.blade.php
    products/
      index.blade.php
  pharmacist/
    dashboard/
      index.blade.php
    prescriptions/
      index.blade.php
  components/
    ui/
      input.blade.php      ← <x-ui.input>
      button.blade.php     ← <x-ui.button>
      card.blade.php       ← <x-ui.card>
    nav/
      admin-nav.blade.php        ← <x-nav.admin-nav>
      salesperson-nav.blade.php
      inventory-nav.blade.php
      pharmacist-nav.blade.php
```

### Rules

- **No loose files** at `resources/views/components/` root — always inside a named subfolder.
- **No shared `dashboard/` folder** — each role owns its own folder tree.
- Feature subfolders use **kebab-case** matching the route group name (`inventory-manager/`, `inventory-manager/products/`).
- The main page of a feature is always named `index.blade.php`. Detail pages use `show.blade.php`, forms use `create.blade.php` / `edit.blade.php`.

---

## Layouts

### `layouts/auth.blade.php`
For login and register. Centered white card on `bg-primary` background. Includes Poppins font + Alpine.js CDN + Vite CSS.

```blade
@extends('layouts.auth')
@section('title', 'Sign In')
@section('content')
    {{-- page content --}}
@endsection
```

### `layouts/app.blade.php`
For all authenticated pages. Yields `nav` (role-specific nav component) and `content`.

```blade
@extends('layouts.app')
@section('title', 'Dashboard')
@section('nav')
    <x-nav.admin-nav />
@endsection
@section('content')
    {{-- page content --}}
@endsection
```

Each role injects its own nav via `@section('nav')` — never hardcode a nav inside the layout.

---

## Components

### Naming convention
Component tag = `<x-{folder}.{file}>` — the dot maps to the subfolder.

| File path | Tag |
|---|---|
| `components/ui/input.blade.php` | `<x-ui.input>` |
| `components/ui/button.blade.php` | `<x-ui.button>` |
| `components/ui/card.blade.php` | `<x-ui.card>` |
| `components/nav/admin-nav.blade.php` | `<x-nav.admin-nav>` |

### `<x-ui.input>` — form field with label and error
```blade
<x-ui.input name="email" label="Email address" type="email" />
<x-ui.input name="password" label="Password" type="password" />
```
- Password fields automatically include an Alpine.js show/hide toggle.
- Validation errors display inline via `@error($name)`.
- `old($name)` is applied automatically for non-password fields.

### `<x-ui.button>` — submit button
```blade
<x-ui.button>Save</x-ui.button>
<x-ui.button variant="secondary">Cancel</x-ui.button>
<x-ui.button variant="outline">Back</x-ui.button>
```
Variants: `primary` (default, `bg-primary`), `secondary` (`bg-secondary`), `outline` (border only).

### `<x-ui.card>` — content card
```blade
<x-ui.card title="Account Info">
    {{-- card content --}}
</x-ui.card>

<x-ui.card>
    {{-- card without title --}}
</x-ui.card>
```

---

## Tailwind CSS

### File location
`resources/tailwind/app.css` — **do not move or rename**.  
Vite entry: set in `vite.config.js` as `input: ['resources/tailwind/app.css']`.

### Theme tokens (use these, never raw hex values in views)
```
bg-primary        #101C5D   dark navy
bg-secondary      #569298   teal
bg-accent         #D4AF37   gold
bg-neutralLight   #F5F5F5
bg-neutralDark    #333333
bg-coral          #F97C7C
```

### Adding new token
Add it to the `@theme` block in `resources/tailwind/app.css`:
```css
@theme {
    --color-brand-new: #RRGGBB;
}
```
Then use it as `bg-brand-new`, `text-brand-new`, etc.

---

## Alpine.js

Loaded via **CDN** (pinned version) in both layouts:
```html
<script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
```

Use for **local interactivity only**: dropdowns, modals, password toggles, tab switches.  
Do **not** use Alpine for data that comes from the server — use Blade for that.

```blade
{{-- toggle example --}}
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

---

## Adding a New Feature Page (checklist)

1. Create the view at `resources/views/{role}/{feature}/index.blade.php`
2. Extend the correct layout: `@extends('layouts.app')`
3. Inject the role's nav: `@section('nav') <x-nav.{role}-nav /> @endsection`
4. Add the route in `routes/web.php` under the correct role middleware group
5. Add the controller method in `app/Http/Controllers/Web/{Role}/{Feature}Controller.php`
6. Return the view: `return view('{role}.{feature}.index')`
