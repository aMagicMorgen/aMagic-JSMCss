# Документация для класса `MCssJS`

## Оглавление
1. [Обзор](#обзор)
2. [Установка и требования](#установка-и-требования)
3. [Формат правил](#формат-правил)
4. [Примеры использования](#примеры-использования)
5. [Методы класса](#методы-класса)
6. [Генерируемый JavaScript](#генерируемый-javascript)
7. [Примечания и ограничения](#примечания-и-ограничения)

---

## 1. Обзор <a name="обзор"></a>
Класс `MCssJS` генерирует JavaScript-код для динамического управления CSS-классами элементов на веб-странице. Он позволяет:
- Добавлять классы к элементам по **тегам**
- Заменять классы у элементов с определённым **классом**
- Добавлять классы к элементам по **ID**

Идеально подходит для:
- Динамического применения стилей без изменения исходного HTML
- Работы с CSS-фреймворками (Tailwind, Bootstrap)
- Управления темами оформления
- Анимаций и интерактивных элементов

---

## 2. Установка и требования <a name="установка-и-требования"></a>
**Требования:**
- PHP 7.4+
- Подключение сгенерированного скрипта к HTML-странице

**Установка:**
```php
// Подключите класс в ваш проект
require_once 'MCssJS.php';
```

---

## 3. Формат правил <a name="формат-правил"></a>
### Синтаксис
```
!селекторы | классы
```
- `!` — начало нового правила
- `селекторы` — список через пробел:
  - `tag` — тег (например, `h1`, `div`)
  - `.class` — класс для замены
  - `#id` — ID элемента
- `|` — разделитель
- `классы` — список классов через пробел (можно переносить на новые строки)

### Особенности обработки
| Селектор  | Действие                                                                 |
|-----------|--------------------------------------------------------------------------|
| `div`     | Добавляет классы ко всем элементам `<div>`                              |
| `.old`    | Заменяет класс `old` на указанные классы                               |
| `#header` | Добавляет классы к элементу с `id="header"`                            |

---

## 4. Примеры использования <a name="примеры-использования"></a>

### Пример 1: Базовое применение
```php
$rules = <<<EOT
!h1 .title | 
text-4xl font-bold 
text-blue-600

!#main-header |
bg-gray-100 p-4 rounded-lg
EOT;

$generator = new MCssJS($rules);
echo "<script>{$generator->generateScript()}</script>";
```
**Результат:**
```javascript
document.addEventListener('DOMContentLoaded', function() {
  // Добавление классов к тегам
  const addClasses = { 'h1': 'text-4xl font-bold text-blue-600' };
  Object.entries(addClasses).forEach(([selector, classes]) => {
    document.querySelectorAll(selector).forEach(el => {
      el.classList.add(...classes.split(' '));
    });
  });

  // Замена классов
  document.querySelectorAll('.title').forEach(el => {
    if (el.classList.contains('title')) {
      el.classList.remove('title');
      el.classList.add(...'text-4xl font-bold text-blue-600'.split(' '));
    }
  });

  // Работа с ID
  {
    const el = document.getElementById('main-header');
    if (el) {
      el.classList.add(...["bg-gray-100", "p-4", "rounded-lg"]);
    }
  }
});
```

### Пример 2: Комплексные правила
```php
$advancedRules = <<<EOT
!button .btn-primary #submit-btn |
px-6 py-2 rounded-full 
transition-all hover:scale-105

!.error-message |
text-red-500 animate-pulse
EOT;

$js = (new MCssJS($advancedRules))->generateScript();
```

---

## 5. Методы класса <a name="методы-класса"></a>

### `__construct(string $styleString)`
- Парсит переданные правила
- Подготавливает данные для генерации JS

```php
$generator = new MCssJS('!div | my-class');
```

### `generateScript(): string`
- Возвращает готовый JavaScript-код
- Код выполняется после полной загрузки DOM

```php
$script = $generator->generateScript();
echo "<script>{$script}</script>";
```

---

## 6. Генерируемый JavaScript <a name="генерируемый-javascript"></a>
Код включает три основных блока:

1. **Добавление классов к тегам**
```javascript
document.querySelectorAll('h1').forEach(el => {
  el.classList.add('text-xl', 'font-bold');
});
```

2. **Замена классов**
```javascript
document.querySelectorAll('.old-class').forEach(el => {
  el.classList.remove('old-class');
  el.classList.add('new-class', 'bg-blue-500');
});
```

3. **Работа с ID**
```javascript
const el = document.getElementById('header');
if (el) {
  el.classList.add('mt-4', 'p-2');
}
```

---

## 7. Примечания и ограничения <a name="примечания-и-ограничения"></a>

### Ограничения
- Не поддерживает сложные селекторы (`div.class`, `input[type="text"]`)
- Нет обработки медиа-запросов
- Не работает с псевдоклассами (`:hover`, `:nth-child()`)

### Рекомендации
1. Всегда экранируйте специальные символы:
   ```php
   !.btn\:primary | custom-class // Экранирование через \
   ```
2. Для динамического обновления (SPA) переинициализируйте класс при изменении DOM
3. Используйте вместе с CSS-фреймворками для быстрого прототипирования

### Ошибки и отладка
| Ситуация                     | Решение                                  |
|------------------------------|------------------------------------------|
| Классы не применяются        | Проверьте синтаксис правил               |
| Конфликты классов            | Используйте уникальные имена классов     |
| Ошибки в консоли браузера    | Убедитесь в корректности генерируемого JS|

---
# aMagic-MCssJs и MCssStatic
MCss (  Manager CSS (JavaScript)) для подстановки css классов в ` голый ` HTML

Документация с примерами и переименованным в class JSMCss

```php
<?php

/**
 * Класс MCssJS — генератор JavaScript-кода для динамического управления CSS-классами элементов на странице.
 *
 * Позволяет на основе текстового описания правил:
 * - Добавлять классы к элементам по тегам
 * - Заменять указанные классы у элементов с определённым классом
 * - Добавлять или заменять классы у элементов по id
 *
 * Формат описания правил:
 * Каждое правило начинается с символа `!`, далее список селекторов (теги, классы `.class`, id `#id`) через пробел,
 * затем разделитель `|`, затем список классов, которые нужно применить.
 * Классы могут быть записаны в одну строку или перенесены на несколько строк.
 *
 * Пример правила:
 * ```
 * !h1 .title #header | font-bold text-wrap:balance
 * ```
 * Это правило:
 * - Добавит классы `font-bold text-wrap:balance` ко всем `<h1>`
 * - Заменит класс `.title` у элементов на `font-bold text-wrap:balance`
 * - Добавит эти классы элементу с id `header`
 *
 * @example
 * $styleString = <<<EOT
 * !h1 .title #header |
 * font-bold
 * text-wrap:balance
 * !.btn | bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
 * !#special | border-2 border-red-500 p-4
 * EOT;
 *
 * $jsManager = new JSMCss($styleString);
 * $script = $jsManager->generateScript();
 * echo "<script>{$script}</script>";
 */
class JSMCss
{
    /** @var array<string, string> Классы для добавления по тегам */
    private array $stylesMap = [];

    /** @var array<string, string> Классы для замены по селекторам с классами */
    private array $replaceSelectors = [];

    /** @var array<string, array{add: string[]}> Классы для добавления по id */
    private array $idSelectors = [];

    /**
     * Конструктор принимает строку с правилами и парсит её
     * @param string $styleString Правила в описанном формате
     */
    public function __construct(string $styleString)
    {
        $this->parseStyleString($styleString);
    }

    /**
     * Парсит строку с правилами в формате с разделителем !
     * @param string $styleString
     * @return void
     */
    private function parseStyleString(string $styleString): void
    {
        // Разбиваем по '!' — каждый блок — отдельное правило
        $blocks = preg_split('/\!/', trim($styleString));
        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') continue;

            // Разбиваем по '|' на селекторы и классы
            $parts = preg_split('/\|/', $block, 2);
            if (count($parts) < 2) continue; // если нет классов — пропускаем

            $selectorsPart = trim($parts[0]);
            $classesPart = trim($parts[1]);

            // Классы могут быть многострочными — заменяем переносы на пробелы и нормализуем пробелы
            $classesPart = preg_replace('/\s+/', ' ', $classesPart);

            // Разбиваем селекторы по пробелу
            $selectors = preg_split('/\s+/', $selectorsPart);

            foreach ($selectors as $selector) {
                if ($selector === '') continue;

                if (strpos($selector, '#') === 0) {
                    // id — добавляем в idSelectors для добавления классов
                    $id = substr($selector, 1);
                    if (!isset($this->idSelectors[$id])) {
                        $this->idSelectors[$id] = ['add' => []];
                    }
                    $this->idSelectors[$id]['add'][] = $classesPart;
                } elseif (strpos($selector, '.') === 0) {
                    // класс — заменяем класс у элементов с этим классом
                    if (!isset($this->replaceSelectors[$selector])) {
                        $this->replaceSelectors[$selector] = '';
                    }
                    if ($this->replaceSelectors[$selector] !== '') {
                        $this->replaceSelectors[$selector] .= ' ';
                    }
                    $this->replaceSelectors[$selector] .= $classesPart;
                } else {
                    // тег — добавляем классы
                    if (!isset($this->stylesMap[$selector])) {
                        $this->stylesMap[$selector] = '';
                    }
                    if ($this->stylesMap[$selector] !== '') {
                        $this->stylesMap[$selector] .= ' ';
                    }
                    $this->stylesMap[$selector] .= $classesPart;
                }
            }
        }
    }

    /**
     * Генерирует JavaScript-код, который динамически применит классы согласно правилам
     * @return string JS-код для вставки в страницу
     */
    public function generateScript(): string
    {
        $js = "document.addEventListener('DOMContentLoaded', function() {\n";

        // Добавление классов к тегам
        $js .= "  const addClasses = {\n";
        foreach ($this->stylesMap as $selector => $classes) {
            $escapedClasses = addslashes($classes);
            $js .= "    '{$selector}': '{$escapedClasses}',\n";
        }
        $js .= "  };\n\n";

        $js .= "  Object.entries(addClasses).forEach(([selector, classes]) => {\n";
        $js .= "    document.querySelectorAll(selector).forEach(el => {\n";
        $js .= "      el.classList.add(...classes.split(' '));\n";
        $js .= "    });\n";
        $js .= "  });\n\n";

        // Замена классов у элементов с классом (replaceSelectors)
        foreach ($this->replaceSelectors as $selector => $classes) {
            $escapedClasses = addslashes($classes);
            $className = substr($selector, 1);
            $js .= "  document.querySelectorAll('{$selector}').forEach(el => {\n";
            $js .= "    if (el.classList.contains('{$className}')) {\n";
            $js .= "      el.classList.remove('{$className}');\n";
            $js .= "      el.classList.add(...'{$escapedClasses}'.split(' '));\n";
            $js .= "    }\n";
            $js .= "  });\n\n";
        }

        // Добавление классов к элементам по id
        foreach ($this->idSelectors as $id => $actions) {
            $js .= "  {\n";
            $js .= "    const el = document.getElementById('{$id}');\n";
            $js .= "    if (el) {\n";
            foreach ($actions['add'] as $classList) {
                $escapedClasses = json_encode(explode(' ', $classList));
                $js .= "      el.classList.add(...{$escapedClasses});\n";
            }
            $js .= "    }\n";
            $js .= "  }\n\n";
        }

        $js .= "});\n";

        return $js;
    }
}
```

---

## Пример использования

```php
<?php
require_once 'MCssJS.php';

$styleString = <<<EOT
!h1 .title #header | font-bold text-wrap:balance
!.btn | bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
!#special | border-2 border-red-500 p-4
EOT;

$jsManager = new JSMCss($styleString);
$script = $jsManager->generateScript();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Пример MCssJS</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <h1>Заголовок</h1>
  <div class="title">Текст с классом title</div>
  <div id="header">Элемент с id header</div>
  <button class="btn">Кнопка</button>
  <div id="special">Особый элемент</div>

  <script>
    <?= $script ?>
  </script>
</body>
</html>
```

---

## Итоги

- Класс `MCssJS` универсален и может работать с любыми CSS-классами.
- Позволяет писать компактные правила с множественными селекторами и классами.
- Генерирует JS для динамического применения стилей без необходимости прописывать классы вручную в HTML.
- Отлично подходит для проектов с динамической или семантической разметкой.

Если понадобится помощь с расширением или интеграцией — обращайтесь!

**Лицензия:** MIT  
**Автор:** Ваше имя/команда  
**Версия:** 1.0.0
