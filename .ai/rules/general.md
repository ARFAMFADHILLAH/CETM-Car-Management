---
paths:
  - composer.json
---

# General

## Jalankan PHPStan dengan env var khusus di mesin ini
`composer run types:check` gagal di mesin ini karena ekstensi turbo PHPStan tidak bisa dimuat ("Dynamic loading not supported"). Gunakan: `PHPSTAN_TURBO_EXT_DISABLED=1 vendor/bin/phpstan analyse --memory-limit=1G`. Tanpa memory-limit juga akan crash di limit 128M default.
