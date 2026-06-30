# FreshRSS QualityFilter

> A quality filter extension for FreshRSS 1.29.x that filters RSS articles before they are imported into the database.

[中文文档](README.zh-CN.md)

---

## ✨ Features

### Phase 1 (v0.1.0)

| Filter | Description |
|--------|-------------|
| **Length Filter** | Minimum character count for title and content (UTF-8, CJK, Emoji support) |
| **Keyword Filter** | Title and content keyword blacklist (contains / exact match) |
| **Regex Filter** | PCRE patterns with configurable scope (title / content / URL) |
| **URL Filter** | URL keyword blacklist (e.g. `utm_`, `tracking`, `share=`) |
| **Feed Filter** | Feed whitelist + blacklist with blacklist priority |
| **Author Filter** | Author name blacklist (e.g. bot, auto-publish) |
| **Debug Logging** | Detailed filter logs with automatic rotation (10MB) |

### Upcoming

- **Phase 2**: Deduplication (SimHash / URL / Title hash)
- **Phase 3**: Reading time estimation
- **Phase 4**: AI quality scoring (OpenAI / Ollama / OpenRouter)

---

## 📦 Installation

### Standard

```bash
cd /path/to/freshrss/extensions/
git clone https://github.com/sungmee/freshRSS-QualityFilter.git
mkdir -p freshRSS-QualityFilter/logs
chmod 755 freshRSS-QualityFilter/logs
```

### Docker — Bind Mount

```bash
docker run -d \
  -v /path/to/freshRSS-QualityFilter:/var/www/FreshRSS/extensions/freshRSS-QualityFilter \
  freshrss/freshrss:1.29
```

### Docker Compose

```yaml
services:
  freshrss:
    image: freshrss/freshrss:1.29
    volumes:
      - ./freshRSS-QualityFilter:/var/www/FreshRSS/extensions/freshRSS-QualityFilter
      - freshrss_data:/var/www/FreshRSS/data
```

### Custom Dockerfile

```dockerfile
FROM freshrss/freshrss:1.29
COPY ./freshRSS-QualityFilter /var/www/FreshRSS/extensions/freshRSS-QualityFilter
RUN mkdir -p /var/www/FreshRSS/extensions/freshRSS-QualityFilter/logs \
    && chown -R www-data:www-data /var/www/FreshRSS/extensions/freshRSS-QualityFilter/logs
```

---

## ⚙️ Configuration

Navigate to **Administration → Extensions** in FreshRSS and click the configure button next to QualityFilter.

### General Settings

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| Enable Plugin | checkbox | On | When disabled, all filter rules are paused |
| Min Content Length | number | 200 | Articles with fewer characters (after stripping HTML & whitespace) are filtered |
| Min Title Length | number | 0 | Articles with shorter titles are filtered; 0 disables the check |
| Filter Action | select | Skip Import | "Mark as Read" will be implemented in Phase 2 |
| Debug Mode | checkbox | Off | Enables detailed logging when turned on |

### Keyword Matching

| Setting | Description |
|---------|-------------|
| Match Mode | Contains: triggers on substring match. Exact: triggers on full string equality (case-insensitive) |
| Title Keywords | One per line. Articles whose title matches any keyword are filtered |
| Content Keywords | One per line. Articles whose content matches any keyword are filtered |

### Other Filters

| Setting | Description |
|---------|-------------|
| URL Blacklist | Articles whose URL contains any keyword are filtered |
| Author Blacklist | Articles whose author name contains any keyword are filtered |
| Feed Whitelist | Leave empty to allow all feeds; otherwise only matching feeds pass |
| Feed Blacklist | Takes priority over the whitelist |
| Regex Rules | One PCRE pattern per line. Use the `u` modifier for UTF-8 |
| Regex Scope | Apply regex to title, content, URL, or all of the above |

---

## 🔧 How It Works

The extension hooks into FreshRSS's `entry_before_insert` event:

```
RSS Feed Update
  ↓
FreshRSS parses articles
  ↓
【EntryBeforeInsert Hook】
  ↓
QualityFilter Chain (Chain of Responsibility)
  ├── LengthFilter       → length check
  ├── KeywordFilter      → keyword check
  ├── RegexFilter        → regex check
  ├── UrlFilter          → URL check
  ├── FeedFilter         → feed whitelist / blacklist
  ├── AuthorFilter       → author check
  └── DuplicateFilter    → dedup (Phase 2)
  ↓
Pass → insert into database
Fail → return null (skip import)
```

### Chain of Responsibility

All filters implement `FilterInterface` and return a `FilterResult` value object. `FilterPipeline` runs filters in registration order, short-circuiting on the first failure.

To add a new filter:
1. Create a class implementing `FilterInterface`
2. Register it in `extension.php`'s pipeline

No existing code needs modification.

---

## 📁 Directory Structure

```
freshRSS-QualityFilter/
├── metadata.json               # Plugin metadata
├── extension.php               # Entry point (extends Minz_Extension)
├── configure.php               # Admin configuration page
├── README.md                   # English documentation (this file)
├── README.zh-CN.md             # 简体中文文档
├── LICENSE                     # MIT License
├── phpunit.xml                 # PHPUnit configuration
├── i18n/
│   ├── en/gen.php              # English translations
│   └── zh-cn/gen.php           # Simplified Chinese translations
├── static/
│   ├── quality.css             # Config page styles
│   └── quality.js              # Config page scripts
├── Services/
│   ├── FilterInterface.php     # Filter interface
│   ├── FilterResult.php        # Filter result value object
│   ├── FilterPipeline.php      # Chain of Responsibility pipeline
│   ├── LengthFilter.php        # Length filter
│   ├── KeywordFilter.php       # Keyword filter
│   ├── RegexFilter.php         # Regex filter
│   ├── UrlFilter.php           # URL filter
│   ├── FeedFilter.php          # Feed filter
│   ├── AuthorFilter.php        # Author filter
│   ├── DuplicateFilter.php     # Dedup filter (Phase 2 placeholder)
│   ├── Utils.php               # Utility functions
│   └── Logger.php              # Logging service
├── tests/                      # Unit tests (96 tests, 120 assertions)
└── logs/                       # Runtime log directory
```

---

## 🧪 Tests

```bash
composer require --dev phpunit/phpunit
./vendor/bin/phpunit
```

96 tests, 120 assertions, all passing.

---

## 🗺️ Roadmap

- [x] **v0.1.0** — Core filters: length, keyword, regex, URL, feed, author, logging
- [ ] **v0.2.0** — Deduplication (SimHash, URL/title hashing)
- [ ] **v0.3.0** — Reading time estimation
- [ ] **v0.4.0** — AI quality scoring (OpenAI / Ollama / OpenRouter)

---

## ❓ FAQ

**Q: Config page won't open after installation?**

A: Verify directory permissions (755 recommended) and ensure FreshRSS ≥ 1.29.0.

**Q: Log file permission denied in Docker?**

A: Set the correct ownership:
```bash
docker exec <container> chown -R www-data:www-data /var/www/FreshRSS/extensions/freshRSS-QualityFilter/logs
```

**Q: A regex rule isn't working?**

A: Invalid patterns are silently skipped. Test your regex locally before adding it.

**Q: Feed whitelist is configured but not taking effect?**

A: Matching is case-insensitive substring. Also check that the feed isn't in the blacklist — blacklist always takes priority.

**Q: How do I verify the plugin is actually filtering?**

A: Enable Debug Mode and check `logs/filter.log`. Every filtered article is recorded with the reason and matched rule.

---

## 📄 License

MIT — see [LICENSE](LICENSE).
