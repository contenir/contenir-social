# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased] - 2026-10-21

### Added

- A catalogue of ten networks for profiles (`Network`): Facebook, Instagram, LinkedIn, X, YouTube, TikTok, Pinterest,
  Threads, Bluesky and Mastodon, each with its settings key, an example for editors and its icon.
- `ProfileUrl`, which reads a profile address in any form its network serves it in, or an account given on its own
  (`@yourname`, `yourname.bsky.social`, `@yourname@server`), checks it against the network's hosts and paths, and keeps
  the canonical https address. Problems are worded for editors.
- `Profiles::fromSettings()`, which reads a site's profiles under the keys the CMS's Social Media Links plugin used
  (`social.social_*`), so accounts stored as handles read as addresses. `sameAs()` lists them for schema.org, and
  `twitterSite()` gives the X account for `twitter:site`.
- Share links (`Share\ShareLinks`) for Facebook, X, LinkedIn, WhatsApp, Email, Bluesky, Threads, Reddit, Pinterest
  (only with an image) and Telegram, plus "Copy link" and "Share…" buttons. They are plain links built from the page's
  metadata (`Share\Page`), with optional UTM tags; no third-party scripts, counts or cookies.
- `Share\ShareConfig::fromSettings()`, which reads which buttons a site shows and in what order, whether names show
  beside icons and whether addresses are tagged (`extensions.social.*`). A site that has chosen no buttons has the
  defaults: Facebook, X, LinkedIn, Email, Copy link and Share….
- HTML for both (`Html\ProfileLinks`, `Html\ShareButtons`): escaped, with `social__*` BEM classes a site can rename,
  `rel="me"` on profiles, accessible names that say when a link opens a new tab, and inline SVG icons
  (`Html\IconSvg`) from Simple Icons and Tabler Icons (see ICONS.md).
- `Script\ShareScript`, a dependency-free script that shows "Copy link" (with a visible, announced "Copied") and,
  only where the browser has a share sheet, "Share…".
