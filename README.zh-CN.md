# FreshRSS QualityFilter

> 适用于 FreshRSS 1.29.x 的 RSS 文章质量过滤插件——在文章导入数据库之前完成多维过滤，自动跳过低质量内容。

[English Documentation](README.md)

---

## ✨ 功能特性

### 第一阶段 (v0.1.0)

| 过滤器 | 说明 |
|--------|------|
| **长度过滤** | 标题和正文最小字符数限制（UTF-8，支持中文和 Emoji） |
| **关键字过滤** | 标题 / 正文关键字黑名单（支持包含匹配和完全匹配） |
| **正则过滤** | PCRE 正则表达式，可配置作用范围（标题 / 正文 / URL） |
| **URL 过滤** | URL 关键字黑名单（如 `utm_`、`tracking`、`share=`） |
| **Feed 过滤** | Feed 白名单 + 黑名单，黑名单优先级高于白名单 |
| **作者过滤** | 作者名黑名单（如机器人、自动发布） |
| **调试日志** | Debug 模式下记录详细过滤日志，自动轮转（10MB） |

### 后续阶段

- **Phase 2**：去重过滤（SimHash / URL / 标题哈希）
- **Phase 3**：阅读时间过滤
- **Phase 4**：AI 质量判定（OpenAI / Ollama / OpenRouter）

---

## 📦 安装

### 标准安装

```bash
cd /path/to/freshrss/extensions/
git clone https://github.com/sungmee/freshRSS-QualityFilter.git
mkdir -p freshRSS-QualityFilter/logs
chmod 755 freshRSS-QualityFilter/logs
```

### Docker — 挂载卷

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

### 自定义 Dockerfile

```dockerfile
FROM freshrss/freshrss:1.29
COPY ./freshRSS-QualityFilter /var/www/FreshRSS/extensions/freshRSS-QualityFilter
RUN mkdir -p /var/www/FreshRSS/extensions/freshRSS-QualityFilter/logs \
    && chown -R www-data:www-data /var/www/FreshRSS/extensions/freshRSS-QualityFilter/logs
```

---

## ⚙️ 配置说明

在 FreshRSS 后台 **管理 → 扩展** 中找到 QualityFilter，点击右侧的配置按钮。

### 基本设置

| 配置项 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| 启用插件 | 复选框 | 开启 | 关闭后所有过滤规则暂停生效 |
| 最小正文字符数 | 数字 | 200 | 正文（去除 HTML 标签和空白后）低于此值的文章被过滤 |
| 最小标题字符数 | 数字 | 0 | 标题低于此值的文章被过滤，设为 0 表示不限制 |
| 过滤动作 | 下拉 | 跳过导入 | "标记已读"将在 Phase 2 实现 |
| 调试模式 | 复选框 | 关闭 | 开启后将详细过滤日志写入 `logs/filter.log` |

### 关键字匹配

| 配置项 | 说明 |
|--------|------|
| 匹配模式 | 包含匹配：文本中出现关键字即触发；完全匹配：文本与关键字完全相等（忽略大小写） |
| 标题关键字 | 一行一个，标题匹配任一关键字的文章被过滤 |
| 正文关键字 | 一行一个，正文匹配任一关键字的文章被过滤 |

### 其他过滤

| 配置项 | 说明 |
|--------|------|
| URL 黑名单 | 文章链接包含任一关键字的被过滤 |
| 作者黑名单 | 作者名包含任一关键字的被过滤 |
| Feed 白名单 | 留空表示允许所有 Feed；指定后仅匹配的 Feed 通过 |
| Feed 黑名单 | 优先级高于白名单 |
| 正则规则 | 一行一个 PCRE 正则，建议使用 `u` 修饰符以支持 UTF-8 |
| 正则范围 | 可选择匹配标题 / 正文 / URL 或全部 |

---

## 🔧 Hook 原理

插件使用 FreshRSS 的 `entry_before_insert` Hook：

```
RSS 更新
  ↓
FreshRSS 解析文章
  ↓
【EntryBeforeInsert Hook】
  ↓
QualityFilter 过滤器链（责任链模式）
  ├── LengthFilter       → 长度检查
  ├── KeywordFilter      → 关键字检查
  ├── RegexFilter        → 正则检查
  ├── UrlFilter          → URL 检查
  ├── FeedFilter         → Feed 白/黑名单
  ├── AuthorFilter       → 作者检查
  └── DuplicateFilter    → 去重（Phase 2）
  ↓
通过 → 写入数据库
未通过 → return null（跳过导入）
```

### 责任链模式

所有过滤器均实现 `FilterInterface` 接口，返回 `FilterResult` 值对象。`FilterPipeline` 按注册顺序依次执行，任一过滤器返回失败即短路停止。

新增过滤器只需两步：
1. 创建类并实现 `FilterInterface`
2. 在 `extension.php` 的 `buildPipeline()` 中注册

无需修改已有代码，完全符合开闭原则。

---

## 📁 目录结构

```
freshRSS-QualityFilter/
├── metadata.json               # 插件元数据
├── extension.php               # 入口文件（继承 Minz_Extension）
├── configure.php               # 后台配置页面
├── README.md                   # 英文文档
├── README.zh-CN.md             # 简体中文文档（本文件）
├── LICENSE                     # MIT 许可证
├── phpunit.xml                 # PHPUnit 配置
├── i18n/
│   ├── en/gen.php              # 英文翻译
│   └── zh-cn/gen.php           # 简体中文翻译
├── static/
│   ├── quality.css             # 配置页面样式
│   └── quality.js              # 配置页面脚本
├── Services/
│   ├── FilterInterface.php     # 过滤器接口
│   ├── FilterResult.php        # 过滤结果值对象（readonly）
│   ├── FilterPipeline.php      # 责任链管道
│   ├── LengthFilter.php        # 长度过滤器
│   ├── KeywordFilter.php       # 关键字过滤器
│   ├── RegexFilter.php         # 正则过滤器
│   ├── UrlFilter.php           # URL 过滤器
│   ├── FeedFilter.php          # Feed 过滤器
│   ├── AuthorFilter.php        # 作者过滤器
│   ├── DuplicateFilter.php     # 去重过滤器（Phase 2 占位）
│   ├── Utils.php               # 工具函数（文本规范化、字符统计等）
│   └── Logger.php              # 日志服务（10MB 自动轮转）
├── tests/                      # 单元测试（96 个测试，120 个断言）
└── logs/                       # 运行时日志目录
```

---

## 🧪 测试

```bash
composer require --dev phpunit/phpunit
./vendor/bin/phpunit
```

96 个测试，120 个断言，全部通过。

---

## 🗺️ Roadmap

- [x] **v0.1.0** — 核心过滤功能：长度、关键字、正则、URL、Feed、作者、日志
- [ ] **v0.2.0** — 去重过滤：SimHash、URL 去重、标题哈希
- [ ] **v0.3.0** — 阅读时间过滤：中文字数、英文单词数、预计阅读时间
- [ ] **v0.4.0** — AI 质量过滤：OpenAI / Ollama / OpenRouter 接口统一

---

## ❓ 常见问题

**Q: 安装后配置页面无法打开？**

A: 确认插件目录权限为 755，且 FreshRSS 版本 ≥ 1.29.0。

**Q: Docker 中日志文件无写入权限？**

A: 将 `logs/` 目录的所有者改为 `www-data`：
```bash
docker exec <容器名> chown -R www-data:www-data /var/www/FreshRSS/extensions/freshRSS-QualityFilter/logs
```

**Q: 某个正则规则不生效？**

A: 无效的正则会被自动跳过（不会报错）。建议先在本地测试正则表达式，确认无误后再添加到配置。

**Q: Feed 白名单设置了但不生效？**

A: Feed 名称匹配是包含匹配（忽略大小写）。同时确认该 Feed 不在黑名单中——黑名单优先级始终高于白名单。

**Q: 如何验证插件正在工作？**

A: 开启 Debug 模式，查看 `logs/filter.log`。每次过滤都会记录文章信息、过滤原因和匹配规则。

---

## 📄 许可证

MIT License — 详见 [LICENSE](LICENSE) 文件。
