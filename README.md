# minecraft mod site

a multi-mod landing site — hub page listing every mod, one detail page per mod with animated download, version selector, features grid, version history and faq. plus a separate admin panel that manages everything.

## folder layout

```
dupe-mod-site/
  index.php              hub page (lists all mods)
  mod.php                ?m=slug renders one mod page
  download.php           ?m=slug&v=version serves the jar + counts
  assets/                style.css + script.js
  admin/
    index.php            mod manager (create, edit, upload, delete)
    auth.php             login / password
    setup.php            first-run password setup
    helpers.php          shared data layer
  data/
    site.json            brand name, tagline, discord link
    mods/<slug>.json     one file per mod (name, text, features, builds)
    downloads/<slug>/    jars per mod
    config.json          hashed admin password
  robots.txt / sitemap.xml
  preview.html           hub preview (no php needed)
  preview_<slug>.html    per-mod previews
  preview_build.py       regenerates previews from data/
```

## setup

1. upload the whole folder to your host's public web root.
2. make `data/` and `data/downloads/` writable by php (775 works on shared hosts).
3. visit `https://yourdomain.com/admin/` — first visit sets your password.
4. replace `yourdomain.com` in `robots.txt` and `sitemap.xml`.

## adding a mod (admin panel)

- **new mod** — type a name, pick loader + icon, hit create. it appears in the list.
- click **upload + edit** on the mod:
  - upload build (.jar/.zip, version, mc versions, changelog) → becomes the latest build
  - edit name, tagline, description, features, icon, loader
  - delete old builds, or delete the whole mod
- **site settings** — change the brand name, tagline, discord link.

mods you add appear on the hub automatically, newest release first.

## content model

each `data/mods/<slug>.json` holds everything for one mod. the slug is generated from the name when you create it (e.g. "Stack Tool" → `stack-tool`). rename the mod later and the slug stays, so links keep working.

## preview

`python preview_build.py` regenerates `preview.html` (hub) and `preview_<slug>.html` (one per mod) from the data folder. open them in a browser — no php needed. click a card in the hub preview to jump to that mod's preview page.

## seo

- each mod page gets its own meta description, og tags, canonical `mod.php?m=slug`, and JSON-LD `SoftwareApplication` schema.
- hub title/keywords come from `site.json`.
- submit `sitemap.xml` in google search console. add new mods to the sitemap if you want them crawled fast.
- keep changelogs fresh — new versions are what crawl engines notice.

## security

- admin password bcrypt-hashed in `data/config.json`.
- `data/` denied by `.htaccess`; php reaches it directly so it still works.
- admin pages send `noindex`.
- hidden input fields don't matter here — every write action re-reads the mod from disk and never trusts the slug for anything but a filename key.
- move `data/` outside webroot on a vps if you want extra paranoia.