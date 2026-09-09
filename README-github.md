# github pages setup (free, no php needed)

github pages is static only, so this version runs on **github releases** — you upload
a mod by making a release, the site reads it automatically. your github account IS the
admin panel, and its login is already password protected.

## one-time setup

1. create a free repo on github.com (name it anything, it will be `YOUR_REPO_NAME`).
2. make a github account if you don't have one — that's `YOUR_GITHUB_USERNAME`.
3. open `assets/config.js` in this folder, put your username + repo name in:
   ```js
   const SITE = {
     owner: "YOUR_GITHUB_USERNAME",
     repo: "YOUR_REPO_NAME",
     ...
   };
   ```
4. upload these files into the repo (github web UI: repo → Add file → Upload files,
   or drag the folder into the browser):
   - index.html, mod.html
   - assets/ (config.js, site.js, style.css)
   - data/mods.json
   - (robots.txt, sitemap.xml if you want)
   - you do NOT need the php files anymore for github hosting.
5. go to repo → **Settings → Pages** → Source → `Deploy from a branch` → `main` → save.
6. wait ~1 minute. site is live at `https://YOUR_GITHUB_USERNAME.github.io/YOUR_REPO_NAME/`.

## uploading a mod (the admin part)

1. go to your repo → **Releases** → **Draft a new release**.
2. **tag the version with the mod prefix first**: `dupex-1.1.0` (slug then dash then version).
3. attach the jar to the assets field.
4. write changelog lines in the description box.
5. Publish. the site updates by itself, no other step.

the slug in the tag must match the slug in `data/mods.json` — the site matches releases to
mods by `slug-` prefix.

## adding a brand new mod

1. on github, open `data/mods.json` → pencil icon to edit.
2. copy the `stacktool` block and edit slug, name, tagline, description, features, icon.
   valid icons: dirt, diamond, netherite, gold, redstone, emerald.
3. commit.
4. make releases tagged `yourslug-VERSION`.

## previewing locally / before pushing

run `python preview_build.py`. it regenerates the php-style previews AND writes
`data/offline.json`, which the static pages fall back to when there's no internet —
open `index.html` and you'll see the hub with the snapshot mods.

## notes

- download counts come straight from github's release stats — real numbers.
- anonymous api calls are rate limited (60/hour per ip), usually a non-issue for a mod site.
- if a page ever shows zero mods, check `assets/config.js` owner/repo spelling. typo = silent failure.
- re-push a version tag to edit a release; the site picks the change up immediately.