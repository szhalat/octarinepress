# OctarinePress

OctarinePress is a WordPress starter theme for custom Gutenberg block development. It uses an OOP PHP structure, Vite, Tailwind CSS, PostCSS, and theme-owned editor/frontend assets.

It is the starter I have used and refined across real client projects over several years, rebranded and cleaned up for public release. The theme is intended as a clean starting point for new custom WordPress projects, not as a general-purpose theme from wordpress.org.

## Quick Start

This is a build-step theme. **Before activating it, install dependencies and build the assets** — otherwise WordPress shows an admin notice that setup is incomplete:

```bash
composer install   # PHP dependencies + autoloader (required)
npm install        # JS/CSS dependencies
npm run build      # compiles assets to dist/ (required once before first load)
cp .env.example .env
```

See [Installation](#installation) and [Environment Variables](#environment-variables) for details.

## Positioning

OctarinePress is a classic/hybrid WordPress starter for custom Gutenberg block development.

It is built for custom agency and client projects where editors need reusable Gutenberg blocks, while developers keep direct control over PHP templates, frontend performance, asset loading, deployment, and project-specific architecture.

This is intentionally not a full site editing theme. For client builds that need strict design control, custom PHP templates and project-owned blocks are often more predictable than moving every template part into the site editor.

For projects that require full site editing, use this theme as an architectural reference rather than converting it in place. A dedicated block-theme starter should use WordPress `templates/`, `parts/`, block markup, navigation blocks, and a separate FSE-first workflow.

## Features

- OOP PHP service registration with Composer PSR-4 autoloading.
- Custom Gutenberg blocks stored in `blocks/`.
- Vite development server with hot reload for theme assets.
- Tailwind CSS 4 and PostCSS asset pipeline.
- Shared frontend and editor styles.
- Responsive image helpers and registered image sizes.
- `theme.json` configuration for editor colors, typography, spacing, and block support.

## Requirements

- WordPress 6.x
- PHP 8.1 or newer
- Composer
- Node.js 20 or newer
- npm

## Installation

Clone or copy the theme into your WordPress themes directory:

```bash
wp-content/themes/octarinepress
```

Install PHP and JavaScript dependencies:

```bash
composer install
npm install
npm run build
```

Create the required local environment file:

```bash
cp .env.example .env
```

Then edit `.env` for your local WordPress URL and theme folder:

```dotenv
APP_ENV=local
JQUERY_ENABLED=true
VITE_SITE_URL=http://your-site.local
VITE_THEME_FOLDER=octarinepress
```

`.env` is required for local development and must not be committed. The repository includes `.env.example` as the template.

Run `npm run build` at least once after cloning. The theme uses `octarine_assets()` to resolve built assets from `dist/manifest.json`, including the preloaded font in `header.php`. If the requested asset is missing from the manifest, WordPress will stop with a clear `Run npm run build in your theme!` message.

## Environment Variables

`APP_ENV`
: Use `local` when running the Vite dev server. Any other value makes the theme load built files from `dist/manifest.json`.

`JQUERY_ENABLED`
: Set to `true` to keep WordPress jQuery registered on the frontend. Set to `false` to deregister it.

`VITE_SITE_URL`
: Local WordPress site URL opened by Vite.

`VITE_THEME_FOLDER`
: Theme directory name under `wp-content/themes/`.

## Development

Start the Vite development server:

```bash
npm run dev
```

In local mode, WordPress loads frontend and editor assets from:

```text
http://localhost:5173/wp-content/themes/octarinepress
```

If your theme folder is renamed, update `VITE_THEME_FOLDER` in `.env`.

The Vite dev server handles frontend and editor CSS/JS in local mode, but the theme still expects `dist/manifest.json` to exist for assets resolved through `octarine_assets()`. Keep the initial build output locally, or run `npm run build` again after changing asset filenames used by that helper.

## Production Build

Build production assets:

```bash
npm run build
```

The build writes compiled assets and `manifest.json` to `dist/`. The `dist/` directory is ignored in Git by default. Build it during deployment or change that policy if your hosting workflow needs committed assets.

## Theme Structure

```text
assets/          Source CSS, JS, fonts, and images
blocks/          Custom Gutenberg blocks
helpers/         Build helper scripts
inc/             PHP theme classes and helpers
page-templates/  Optional page template directory
patterns/        Optional block pattern directory
template-parts/  Reusable PHP template parts
theme.json       WordPress editor and style settings
vite.config.js   Vite build configuration
```

## Custom Blocks

Blocks live in `blocks/{block-name}` and are registered from `inc/Api/Gutenberg.php`.

Each block can include:

```text
block.json
index.jsx
edit.jsx
save.jsx
render.php
```

If a block has `render.php`, the theme registers it as a dynamic block render callback.

## Git Policy

The repository should include source files and lockfiles, but not local/generated dependency folders:

- Commit: `composer.json`, `composer.lock`, `package.json`, `package-lock.json`, source files, `theme.json`, `.env.example`.
- Do not commit: `.env`, `node_modules/`, `vendor/`, `dist/`.

## Credits

OctarinePress draws on ideas from two projects:

- Vite integration and asset pipeline inspired by [Press Wind](https://github.com/WP-Performance/press-wind).
- OOP service structure based on [Alecaddd/awps](https://github.com/Alecaddd/awps).

## License

GPL v2 or later. See the [`LICENSE`](LICENSE) file and the `style.css` theme header.

---

Built by **Szymon Hałat** — WordPress developer. More work at [halat.dev](https://halat.dev).
