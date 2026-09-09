import json, html, os

base = os.path.dirname(os.path.abspath(__file__))
mods_dir = os.path.join(base, "data", "mods")

def esc(s):
    return html.escape(str(s))

def version_key(v):
    parts = []
    for p in v.replace("-", ".").split("."):
        parts.append(int(p) if p.isdigit() else 0)
    return parts

with open(os.path.join(base, "data", "site.json"), encoding="utf-8") as f:
    site = json.load(f)

ICONS = ["dirt", "diamond", "netherite", "gold", "redstone", "emerald"]

ADMIN_HEAD = """<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin — {brand}</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="admin-body">
<div class="topadmin">
  <h1>ADMIN · {brand}</h1>
  <a href="#">log out</a>
</div>
<main class="admin-shell">
"""

def build_admin(mods):
    brand = site["brand"]
    panels = []
    for m in mods:
        builds = "".join(
            f'''<div class="ver-card">
            <span class="vtag">{'LATEST · ' if i == 0 else ''}v{esc(f['version'])}</span>
            <span class="vmeta">{esc(' · '.join(f['mc_versions']))} · {esc(f['file'])} · {esc(f['size_mb'])} MB</span>
            <form onsubmit="return confirm('delete v{esc(f['version'])} and its file?');">
              <input type="hidden" name="action" value="delete-build">
              <button class="btn btn-danger" type="submit">delete</button>
            </form>
          </div>'''
            for i, f in enumerate(m["files"])
        )
        panels.append(f'''<div class="admin-panel">
  <div class="topadmin" style="border:none; padding:0 0 10px 0;">
    <h1 style="font-family:var(--pixel); font-size:13px; color:var(--grass-bright);">
      {esc(m['name'])}
      <span class="small muted" style="font-family:var(--sans);"> {len(m['files'])} build(s) · {m['downloads']:,} downloads</span>
    </h1>
    <form onsubmit="return confirm('delete the whole mod and all its files?');">
      <button class="btn btn-danger" style="padding:6px 12px; font-size:12px; margin:0;" type="submit">delete mod</button>
    </form>
  </div>

  <h2>UPLOAD BUILD</h2>
  <form>
    <label>Mod file (.jar / .zip)</label>
    <input type="file" name="modfile" accept=".jar,.zip" required>
    <label>Version (e.g. 1.1.0)</label>
    <input type="text" name="version" placeholder="1.1.0" required>
    <label>Minecraft versions (comma separated)</label>
    <input type="text" name="mc_versions" placeholder="1.20.1, 1.21" required>
    <label>Changelog (one line per entry)</label>
    <textarea name="changelog"></textarea>
    <button class="btn" type="submit">Upload build</button>
  </form>

  <div style="height:18px;"></div>

  <h2>BUILDS ({len(m['files'])})</h2>
  {builds}

  <div style="height:18px;"></div>

  <details>
    <summary style="cursor:pointer; color:var(--muted); font-size:14px; margin-bottom:6px;">edit text, features, icon</summary>

    <form>
      <label>Display name</label>
      <input type="text" name="name" value="{esc(m['name'])}">
      <label>Tagline</label>
      <input type="text" name="tagline" value="{esc(m['tagline'])}">
      <label>Modloader</label>
      <select name="modloader">
        <option selected>{esc(m['modloader'])}</option>
      </select>
      <label>Description</label>
      <textarea name="description">{esc(m['description'])}</textarea>
      <label>Features (one per line)</label>
      <textarea name="features">{esc(chr(10).join(m['features']))}</textarea>
      <label>Icon</label>
      <select name="icon">
        <option selected>{esc(m['icon']).capitalize()}</option>
      </select>
      <button class="btn" type="submit">Save changes</button>
    </form>
  </details>

  <p class="small muted" style="margin-top:10px;">
    <a href="preview_{esc(m['slug'])}.html" target="_blank" rel="noopener">view mod page ↗</a>
  </p>
</div>''')

    icons_sel = "".join(f'<option value="{ic}">{ic.capitalize()}</option>' for ic in ICONS)
    page = ADMIN_HEAD.format(brand=esc(brand)) + f'''
  <div class="admin-panel">
    <h2>NEW MOD</h2>
    <form>
      <label>Mod name</label>
      <input type="text" name="name" placeholder="e.g. StackTool" required>
      <label>Tagline (one line under the name)</label>
      <input type="text" name="tagline" placeholder="the stack tool you never knew you needed">
      <label>Modloader</label>
      <select name="modloader">
        <option value="Fabric" selected>Fabric</option>
        <option value="Forge">Forge</option>
        <option value="NeoForge">NeoForge</option>
        <option value="Quilt">Quilt</option>
      </select>
      <label>Icon</label>
      <select name="icon">
        {icons_sel}
      </select>
      <button class="btn" type="submit">Create mod</button>
    </form>
    <p class="small muted">after creating, you will see it below. upload its first build there.</p>
  </div>

  {''.join(panels)}

  <div class="admin-panel">
    <h2>SITE SETTINGS</h2>
    <form>
      <label>Site name (header brand)</label>
      <input type="text" name="brand" value="{esc(brand)}">
      <label>Site tagline</label>
      <input type="text" name="tagline" value="{esc(site['tagline'])}">
      <label>Discord link</label>
      <input type="text" name="discord" value="{esc(site['discord'])}">
      <button class="btn btn-gold" type="submit">Save site settings</button>
    </form>
  </div>

</main>
</body>
</html>'''
    with open(os.path.join(base, "preview_admin.html"), "w", encoding="utf-8") as f:
        f.write(page)

HEAD = """<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{title}</title>
<meta name="description" content="{desc}">
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body data-mod="{brand}">
"""

FOOT = """
<footer>
  <div class="foot-cube">
    <span class="cube dirt"><i class="top"></i><i class="left"></i><i class="right"></i></span>
  </div>
  <p>hand made, no bloat. {brand} mods are fan mods, not affiliated with Mojang or Microsoft.</p>
  <p class="small"><a class="admin-link" href="admin/">admin</a></p>
</footer>

<script src="assets/script.js"></script>
</body>
</html>"""

TICKER_ITEMS = [
    ("dirt", "DIRT"), ("diamond", "DIAMOND"), ("gold", "GOLD INGOT"),
    ("redstone", "REDSTONE"), ("emerald", "EMERALD"), ("netherite", "NETHERITE"),
    ("diamond", "SHULKER BOX"),
]

def cube(cls, size=""):
    return f'<span class="cube {cls}"><i class="top"></i><i class="left"></i><i class="right"></i></span>'

def ticker_html(items):
    return "".join(
        f'<span class="ticker-item">{cube(t)} {label}</span>' for t, label in items
    )

def loads():
    out = []
    for fn in sorted(os.listdir(mods_dir)):
        if fn.endswith(".json") and fn != "..":
            with open(os.path.join(mods_dir, fn), encoding="utf-8") as f:
                m = json.load(f)
            if m.get("files"):
                m["files"] = sorted(m["files"], key=lambda x: version_key(x["version"]), reverse=True)
                m["latest"] = m["files"][0]
            out.append(m)
    out.sort(key=lambda m: m.get("latest", {}).get("date", "0000"), reverse=True)
    return out

def build_hub(mods):
    brand = site["brand"]
    total = sum(int(m["downloads"]) for m in mods)
    cards = ""
    if not mods:
        cards = '<div class="panel reveal"><p>no mods here yet. check back soon.</p></div>'
    for m in mods:
        lat = m["latest"]
        icon = m["icon"] if m["icon"] in ICONS else "diamond"
        cards += f'''
    <a class="mod-card reveal" href="preview_{esc(m['slug'])}.html">
      {cube(icon)}
      <div class="mod-info">
        <div class="mod-name">
          <h3>{esc(m['name'])}</h3>
          <span class="badge mini"><span class="dot"></span>{esc(m['modloader'])}</span>
        </div>
        <p class="mod-tag">{esc(m['tagline'])}</p>
        <div class="mod-meta">
          <span class="chip">v{esc(lat['version'])}</span>
          <span class="chip">MC {esc(' · MC '.join(lat['mc_versions']))}</span>
          <span class="chip">{m['downloads']:,} downloads</span>
        </div>
      </div>
      <span class="mod-arrow">→</span>
    </a>'''

    page = HEAD.format(title=esc(f"{brand} — {site['tagline']}"), desc=esc(site['tagline']), brand=esc(brand)) + f'''
<header>
  <div class="topbar">
    <a class="logo" href="preview.html"><span class="cube-mini"></span>{esc(brand)}</a>
    <nav>
      <a href="#mods">Mods</a>
    </nav>
  </div>
</header>

<main>
  <section class="hero">
    <span class="hero-kicker">Minecraft Mods</span>
    <h1 class="hero-title">{esc(brand)}</h1>
    <p class="hero-tag">{esc(site['tagline'])}</p>
    <div class="badges">
      <span class="badge"><span class="dot"></span>{len(mods)} mods</span>
      <span class="badge gray"><span class="dot"></span>{total:,} downloads</span>
    </div>
  </section>
</main>

<div class="ticker">
  <div class="ticker-track">{ticker_html([("dirt", "NO BLOAT"), ("diamond", "CLIENT SIDE"), ("gold", "OPEN JARS"), ("redstone", "NO PHONE HOME"), ("netherite", "HAND BUILT"), ("emerald", "FREE FOREVER")])}</div>
</div>

<main>
  <section id="mods">
    <div class="section-head reveal">
      <h2>THE MODS</h2>
      <p>pick one, read nothing, press the green button</p>
    </div>
    {cards}
  </section>
</main>
''' + FOOT.format(brand=esc(brand))
    with open(os.path.join(base, "preview.html"), "w", encoding="utf-8") as f:
        f.write(page)

def build_mod(m):
    brand = site["brand"]
    lat = m["latest"]
    icon = m["icon"] if m["icon"] in ICONS else "diamond"
    title = f"Download {m['name']} Mod for Minecraft {', '.join(lat['mc_versions'])}"
    desc = m["description"]
    themes = ICONS
    badges = (
        f'<span class="badge"><span class="dot"></span>v{esc(lat["version"])}</span>\n'
        f'      <span class="badge gold"><span class="dot"></span>{esc(m["modloader"])}</span>\n'
        f'      <span class="badge blue"><span class="dot"></span>{len(lat["mc_versions"])} MC versions</span>\n'
        f'      <span class="badge gray"><span class="dot"></span>{m["downloads"]:,} downloads</span>'
    )
    options = "".join(
        f'<option value="{esc(f["version"])}" {"selected" if i == 0 else ""}>'
        f'v{esc(f["version"])} — MC {esc(" / ".join(f["mc_versions"]))}</option>'
        for i, f in enumerate(m["files"])
    )
    feat_cards = "".join(
        f'''<div class="feature-card reveal">
          <span class="crown"></span>
          {cube(themes[i % len(themes)])}
          <h3>{esc(f)}</h3>
        </div>'''
        for i, f in enumerate(m["features"])
    )
    ver_rows = "".join(
        f'''<div class="ver-row reveal">
          <span class="ver-tag {"" if i else "latest"}">{"" if i else "LATEST · "}v{esc(f["version"])}</span>
          <div class="ver-mcs">{' '.join(f'<span>MC {esc(v)}</span>' for v in f["mc_versions"])}</div>
          <div class="ver-meta"><b>{esc(f["file"])}</b> {esc(f["size_mb"])} MB · {esc(f["date"])}</div>
          {"<div class='ver-change'>" + esc(" / ".join(reversed(f["changelog"]))) + "</div>" if f.get("changelog") else ""}
        </div>'''
        for i, f in enumerate(m["files"])
    )
    ticker_items = [(icon, m["name"].upper())] + TICKER_ITEMS
    faqs = [
        ("Does it work on a server?",
         "Depends on the mod. Any op-limited feature needs operator permissions. Singleplayer always works out of the box."),
        ("Will it crash my world?",
         "No entity spam, no chunk corruption, no network calls. Back up your world before big experiments anyway — that is just good habits."),
        ("Is it a virus?",
         "No. Every jar is tiny and open for inspection. Some antivirus programs flag any mod because of obfuscated class names — grab it from this page and verify the hash if you care."),
    ]
    faq_html = "".join(
        f'''<div class="faq-item">
        <button class="faq-q">{esc(q)} <span class="plus">+</span></button>
        <div class="faq-a"><div>{esc(a)}</div></div>
      </div>'''
        for q, a in faqs
    )

    page = HEAD.format(title=esc(title), desc=esc(desc[:155]), brand=esc(brand)) + f'''
<header>
  <div class="topbar">
    <a class="logo" href="preview.html"><span class="cube-mini"></span>{esc(brand)}</a>
    <nav>
      <a href="preview.html">Mods</a>
      <a href="#download">Download</a>
      <a href="#features">Features</a>
      <a href="#versions">Versions</a>
      <a href="#faq">FAQ</a>
    </nav>
  </div>
</header>

<div id="builds" style="display:none">{esc(json.dumps(m["files"], ensure_ascii=False))}</div>

<main>
  <section class="hero">
    <span class="hero-kicker">{esc(m['modloader'])} Mod</span>
    <h1 class="hero-title">{esc(m['name'])}<br><span class="accent">Download</span></h1>
    <p class="hero-tag">{esc(m['tagline'])}</p>
    <div class="badges">
      {badges}
    </div>
    <div class="download-box" id="download">
      <div class="dl-top">
        <span class="dl-file" id="dl-file-label">{esc(lat['file'])}</span>
        <span class="dl-size" id="dl-size-label">{esc(lat['size_mb'])} MB</span>
      </div>
      <div class="dl-ver">
        <label for="dl-version">Pick a build</label>
        <select id="dl-version">
          {options}
        </select>
      </div>
      <a class="dl-btn" id="dl-link" href="#">
        <span>DOWNLOAD MOD</span><span class="arrow">⇩</span>
      </a>
      <p class="dl-note">drop the jar into your mods folder. needs {esc(m['modloader'])} Loader + API.</p>
      <div class="loaders">
        <span class="loader-chip"><b>{esc(m['modloader'])}</b> Loader</span>
        <span class="loader-chip"><b>{esc(m['modloader'])}</b> API</span>
        <span class="loader-chip"><b>{esc(' · '.join(lat['mc_versions']))}</b></span>
      </div>
    </div>
  </section>
</main>

<div class="ticker">
  <div class="ticker-track">{ticker_html(ticker_items)}</div>
</div>

<main>
  <section id="features">
    <div class="section-head reveal">
      <h2>FEATURES</h2>
      <p>what you actually get when you stop and read the page</p>
    </div>
    <div class="feature-grid">
      {feat_cards}
    </div>
  </section>

  <section>
    <div class="section-head reveal">
      <h2>IN THE JAR</h2>
    </div>
    <div class="panel reveal">
      <p>{esc(desc)}</p>
    </div>
  </section>

  <section id="versions">
    <div class="section-head reveal">
      <h2>VERSION HISTORY</h2>
      <p>every build, kept around. old saves stay happy.</p>
    </div>
    <div class="ver-table">
      {ver_rows}
    </div>
  </section>

  <section id="faq">
    <div class="section-head reveal">
      <h2>FAQ</h2>
      <p>the questions everyone asks in the discord, answered</p>
    </div>
    <div class="faq reveal">
      {faq_html}
    </div>
  </section>
</main>
''' + FOOT.format(brand=esc(brand))
    with open(os.path.join(base, f"preview_{m['slug']}.html"), "w", encoding="utf-8") as f:
        f.write(page)

mods = loads()
build_hub(mods)
for m in mods:
    build_mod(m)
build_admin(mods)

# static-site offline snapshot so index.html / mod.html work without github
import random
with open(os.path.join(base, "data", "mods.json"), encoding="utf-8") as f:
    static_mods = json.load(f)
releases = []
for m in static_mods:
    v = "1.0.0"
    releases.append({
        "tag_name": f"{m['slug']}-{v}",
        "name": f"{m['name']} {v}",
        "body": "Initial release. Works with anything.\n- one line per changelog entry\n- second changelog line",
        "published_at": "2026-09-01T12:00:00Z",
        "assets": [{
            "name": f"{m['slug']}-{v}.jar",
            "size": 1400000,
            "download_count": random.randint(40, 900),
            "browser_download_url": f"https://github.com/OWNER/REPO/releases/download/{m['slug']}-{v}/{m['slug']}-{v}.jar"
        }]
    })
with open(os.path.join(base, "data", "offline.json"), "w", encoding="utf-8") as f:
    json.dump({"mods": static_mods, "releases": releases}, f, ensure_ascii=False, indent=2)

print(f"written preview.html + {len(mods)} mod previews + admin preview + offline.json")