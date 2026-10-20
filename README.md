# contenir/social

[![Continuous Integration](https://github.com/contenir/contenir-social/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/contenir/contenir-social/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/contenir/contenir-social/graph/badge.svg)](https://codecov.io/gh/contenir/contenir-social)

Framework-free social profiles and share links for [Contenir](https://github.com/contenir) sites:

- a catalogue of ten networks, with each one's profile addresses, settings key and icon;
- profile checks: an address in any form the network serves it in, or just the account, becomes the canonical
  https address, and anything else gets a message an editor can act on;
- share links built from the page's own metadata, with no third-party scripts, counts or cookies, so nothing reaches
  a network until a visitor clicks;
- escaped HTML for both, with inline SVG icons and accessible names;
- a small dependency-free script for "Copy link" and "Share…".

It has no framework dependency. Sites use it through an adapter, and the CMS through an add-on:

- **Laminas MVC sites:** [contenir/social-laminas-mvc](https://github.com/contenir/contenir-social-laminas-mvc) adds
  `socialProfiles()` and `share()` view helpers.
- **The CMS:** the [Social add-on](https://github.com/contenir-extension/social) (`contenir-extension/social`) holds
  the settings, checks each profile as it is saved and turns stored handles into addresses.

## Requirements

- PHP 8.3, 8.4 or 8.5

## Profiles

```php
use Contenir\Social\Html\ProfileLinks;
use Contenir\Social\Profiles;

$profiles = Profiles::fromSettings($settings);   // the site's settings, as nested arrays

echo ProfileLinks::render($profiles);
```

writes, for each profile in the order below:

```html
<ul class="social social--profiles">
  <li class="social__item social__item--instagram">
    <a class="social__link" href="https://www.instagram.com/yourname/" rel="me noopener" target="_blank"
       aria-label="Instagram (opens in a new tab)"><svg class="social__icon" …></svg></a>
  </li>
</ul>
```

`rel="me"` tells the networks that check it (Mastodon, for one) that the site and the profile belong together.
`Profiles` also gives `sameAs()`, every address for schema.org, and `twitterSite()`, the X account as `@yourname` for
`twitter:site`.

| Network | Settings key | Accepts | Stored as |
|---|---|---|---|
| Facebook | `social.social_facebook` | facebook.com, www., m., web., mbasic., fb.com; a page, `pages/…` or `profile.php?id=…`; or the username | `https://www.facebook.com/yourpage` |
| Instagram | `social.social_instagram` | instagram.com, www.; or the username | `https://www.instagram.com/yourname/` |
| LinkedIn | `social.social_linkedin` | linkedin.com, www. or a country (au.); `company/`, `in/`, `school/`, `showcase/` or `groups/` | `https://www.linkedin.com/company/yourname/` |
| X | `social.social_twitter` | x.com, twitter.com, www., mobile.; or the username | `https://x.com/yourname` |
| YouTube | `social.social_youtube` | youtube.com, www., m.; `@handle`, `channel/UC…`, `c/…`, `user/…`; or the handle | `https://www.youtube.com/@yourname` |
| TikTok | `social.social_tiktok` | tiktok.com, www., m.; or the username | `https://www.tiktok.com/@yourname` |
| Pinterest | `social.social_pinterest` | pinterest.com and its country domains (pinterest.com.au, au.pinterest.com…); or the username | `https://www.pinterest.com/yourname/` |
| Threads | `social.social_threads` | threads.com, threads.net, www.; or the username | `https://www.threads.com/@yourname` |
| Bluesky | `social.social_bluesky` | bsky.app/profile/…; or the handle (`yourname.bsky.social`, or a domain) | `https://bsky.app/profile/yourname.bsky.social` |
| Mastodon | `social.social_mastodon` | any server's `/@yourname`; or `@yourname@server` | `https://server/@yourname` |

The first five keys are the ones the CMS's Social Media Links plugin used, so a site's existing settings read as they
are. That plugin stored Instagram, X (as Twitter) and Pinterest as usernames, sometimes with the `@` its hint
suggested; they read as addresses. LinkedIn needs the whole address, since a name alone could be a company or a
person. A value that isn't a profile is left out, and `Profiles::$problems` says why, by key.

`ProfileUrl::parse()` reads one value (and throws), `tryParse()` returns null instead, and `problem()` returns the
message for an editor.

## Share buttons

```php
use Contenir\Social\Html\ShareButtons;
use Contenir\Social\Script\ShareScript;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareLinks;

$page  = new Page($canonicalUrl, $title, $description, $shareImage);   // absolute addresses
$links = (new ShareLinks(ShareConfig::fromSettings($settings)))->for($page);

echo ShareButtons::render($links);
echo ShareScript::tag($nonce);   // once per page, for "Copy link" and "Share…"
```

| Button | Goes to | Settings name |
|---|---|---|
| Facebook | `https://www.facebook.com/sharer/sharer.php?u=…` | `facebook` |
| X | `https://x.com/intent/tweet?text=…&url=…` | `x` |
| LinkedIn | `https://www.linkedin.com/sharing/share-offsite/?url=…` | `linkedin` |
| WhatsApp | `https://wa.me/?text=…` | `whatsapp` |
| Email | `mailto:?subject=…&body=…` | `email` |
| Bluesky | `https://bsky.app/intent/compose?text=…` | `bluesky` |
| Threads | `https://www.threads.com/intent/post?text=…&url=…` | `threads` |
| Reddit | `https://www.reddit.com/submit?url=…&title=…` | `reddit` |
| Pinterest | `https://www.pinterest.com/pin/create/button/?url=…&media=…&description=…`, only when the page has an image | `pinterest` |
| Telegram | `https://t.me/share/url?url=…&text=…` | `telegram` |
| Copy link | copies the address | `copy` |
| Share… | the browser's share sheet | `share` |

Facebook's documented share dialog needs a Facebook app ID; `sharer.php` needs none, and still works, but Facebook no
longer documents it.

`ShareConfig::fromSettings()` reads:

| Key | Holds | When missing |
|---|---|---|
| `extensions.social.share_networks` | the buttons, in order: a list of names, the CMS's repeating rows, or names separated by commas | Facebook, X, LinkedIn, Email, Copy link, Share…, also when none are chosen |
| `extensions.social.share_labels` | show each button's name beside its icon | on |
| `extensions.social.share_utm` | add `utm_source` (the button's name), `utm_medium` (`social`, or `email`) and `utm_campaign=share` to the shared address | off |

Links to networks open a new tab and say so in their accessible name ("Share on X (opens in a new tab)"); Email opens
the visitor's mail program. "Copy link" and "Share…" are buttons, written hidden.

### The script

`ShareScript::tag()` writes [resources/share.js](resources/share.js) inline, with the page's CSP nonce. It has no
dependencies, sends nothing anywhere, and does nothing if included twice. It:

- shows "Copy link", and on a click copies the address and shows "Copied" beside the button for two seconds, in a
  `role="status"` element so screen readers announce it;
- shows "Share…" only where the browser has a share sheet (`navigator.share`), which is mostly phones, and opens it
  with the page's address and title.

Without the script, both buttons stay hidden and every other button still works.

## HTML options

Both renderers take `Html\Options`:

```php
new Options(
    icons: true,       // draw icons
    labels: null,      // names beside icons: null is profiles off, share buttons as the site's setting
    newTab: true,      // open links in a new tab, and say so
    block: 'social',   // the BEM block: social, social__item, social__link, social__button, social__icon…
    class: '',         // more classes for the list
);
```

Classes are `{block}`, `{block}--profiles` or `{block}--share` on the list, then `{block}__item` and
`{block}__item--{network}`, `{block}__link`, `{block}__button`, `{block}__icon`, `{block}__label` and
`{block}__status`. Pass `block: 'footer'` to drop them into a site's own styles. No CSS is included.

Icons are 24 by 24 and filled with `currentColor`, so they take the text colour; size them with CSS. They are hidden
from assistive technology, since each link or button carries the name. They come from Simple Icons and Tabler Icons:
see [ICONS.md](ICONS.md) for their licences and the networks' brand guidelines.

## Licence

BSD-3-Clause. See [LICENSE.md](LICENSE.md). Icons: see [ICONS.md](ICONS.md).
