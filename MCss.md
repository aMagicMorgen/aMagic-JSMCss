я все подумал
 1) и назвал class MCss
2) всё-таки разделители должны быть такие как в файле css

h1 .title #header {font-bold text-wrap:balance }
.btn {
bg-blue-500 
hover:bg-blue-700 
text-white 
font-bold 
py-2 
px-4 
rounded
}

 и глазу привычно, что это всё-таки css но с классами внутри и записывать такой синтаксис нужно в style.mcss

Первым делом из всего текста нужно убрать все коментарии, первым разделителем на массив будет } вторым { и дальше все по тому же алгоритму, что в нашем классе. 
хорошо было бы еще учесть обращение к input[tipe=submit] если это возможно реализовать. Напиши код нового класса

```php
<?php

/**
 * Класс MCss — парсер кастомного CSS-подобного формата с классами внутри.
 *
 * Формат похож на CSS:
 * селекторы { классы }
 *
 * Пример:
 * h1 .title #header { font-bold text-wrap:balance }
 * .btn {
 *   bg-blue-500
 *   hover:bg-blue-700
 *   text-white
 *   font-bold
 *   py-2
 *   px-4
 *   rounded
 * }
 *
 * Поддерживает селекторы тегов, классов, id, а также атрибутные селекторы, например:
 * input[type=submit] { btn-submit }
 *
 * Генерирует JS-скрипт, который динамически добавляет/заменяет классы согласно правилам.
 */
class MCss
{
    private $stylesMap = [];
    private $replaceSelectors = [];
    private $idSelectors = [];
    private $attrSelectors = [];

    /**
     * Конструктор принимает весь текст стилей и парсит его
     * @param string $styleText
     */
    public function __construct(string $styleText)
    {
        $cleaned = $this->removeComments($styleText);
        $this->parseStyleText($cleaned);
    }

    /**
     * Удаляет все CSS-подобные комментарии /* ... *\/
     * @param string $text
     * @return string
     */
    private function removeComments(string $text): string
    {
        return preg_replace('#/\*.*?\*/#s', '', $text);
    }

    /**
     * Парсит текст в формате CSS-подобном с классами внутри
     * @param string $text
     * @return void
     */
    private function parseStyleText(string $text): void
    {
        // Разбиваем по закрывающей фигурной скобке }
        $blocks = preg_split('/\}/', $text);

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') continue;

            // Разбиваем по открывающей фигурной скобке {
            $parts = preg_split('/\{/', $block, 2);
            if (count($parts) < 2) continue;

            $selectorsPart = trim($parts[0]);
            $classesPart = trim($parts[1]);

            // Классы могут быть многострочными, заменяем переводы строк и табуляции на пробелы
            $classesPart = preg_replace('/\s+/', ' ', $classesPart);

            // Разбиваем селекторы по запятой (поддержка нескольких селекторов через запятую)
            $selectors = preg_split('/\s*,\s*/', $selectorsPart);

            foreach ($selectors as $selector) {
                $selector = trim($selector);
                if ($selector === '') continue;

                // Определяем тип селектора
                if (preg_match('/^#([\w\-]+)$/', $selector, $m)) {
                    // id селектор
                    $id = $m[1];
                    if (!isset($this->idSelectors[$id])) {
                        $this->idSelectors[$id] = [];
                    }
                    $this->idSelectors[$id][] = $classesPart;
                } elseif (preg_match('/^\.[\w\-]+$/', $selector)) {
                    // класс
                    if (!isset($this->replaceSelectors[$selector])) {
                        $this->replaceSelectors[$selector] = '';
                    }
                    if ($this->replaceSelectors[$selector] !== '') {
                        $this->replaceSelectors[$selector] .= ' ';
                    }
                    $this->replaceSelectors[$selector] .= $classesPart;
                } elseif (preg_match('/^\w+$/', $selector)) {
                    // простой тег
                    if (!isset($this->stylesMap[$selector])) {
                        $this->stylesMap[$selector] = '';
                    }
                    if ($this->stylesMap[$selector] !== '') {
                        $this->stylesMap[$selector] .= ' ';
                    }
                    $this->stylesMap[$selector] .= $classesPart;
                } elseif (preg_match('/^(\w+)?\[(.+)\]$/', $selector, $m)) {
                    // атрибутный селектор, например input[type=submit]
                    $tag = $m[1] ?: '*'; // если тег не указан, выбираем *
                    $attr = $m[2];
                    // Сохраняем для обработки
                    $this->attrSelectors[] = [
                        'tag' => $tag,
                        'attr' => $attr,
                        'classes' => $classesPart,
                    ];
                } else {
                    // Неизвестный селектор — можно логировать или игнорировать
                    // Для безопасности игнорируем
                }
            }
        }
    }

    /**
     * Генерирует JS-скрипт для динамического применения классов
     * @return string
     */
    public function generateScript(): string
    {
        $js = "document.addEventListener('DOMContentLoaded', function() {\n";

        // Добавление классов к тегам
        $js .= "  const addClasses = {\n";
        foreach ($this->stylesMap as $selector => $classes) {
            $escaped = addslashes($classes);
            $js .= "    '{$selector}': '{$escaped}',\n";
        }
        $js .= "  };\n\n";

        $js .= "  Object.entries(addClasses).forEach(([selector, classes]) => {\n";
        $js .= "    document.querySelectorAll(selector).forEach(el => {\n";
        $js .= "      el.classList.add(...classes.split(' '));\n";
        $js .= "    });\n";
        $js .= "  });\n\n";

        // Замена классов у элементов с классом (replaceSelectors)
        foreach ($this->replaceSelectors as $selector => $classes) {
            $escaped = addslashes($classes);
            $className = substr($selector, 1);
            $js .= "  document.querySelectorAll('{$selector}').forEach(el => {\n";
            $js .= "    if (el.classList.contains('{$className}')) {\n";
            $js .= "      el.classList.remove('{$className}');\n";
            $js .= "      el.classList.add(...'{$escaped}'.split(' '));\n";
            $js .= "    }\n";
            $js .= "  });\n\n";
        }

        // Добавление классов по id
        foreach ($this->idSelectors as $id => $classesList) {
            $js .= "  {\n";
            $js .= "    const el = document.getElementById('{$id}');\n";
            $js .= "    if (el) {\n";
            foreach ($classesList as $classes) {
                $escapedClasses = json_encode(explode(' ', $classes));
                $js .= "      el.classList.add(...{$escapedClasses});\n";
            }
            $js .= "    }\n";
            $js .= "  }\n\n";
        }

        // Обработка атрибутных селекторов
        foreach ($this->attrSelectors as $sel) {
            $tag = $sel['tag'];
            $attr = $sel['attr'];
            $classes = $sel['classes'];
            $escapedClasses = addslashes($classes);

            // Формируем селектор CSS
            $cssSelector = $tag . '[' . $attr . ']';

            $js .= "  document.querySelectorAll('{$cssSelector}').forEach(el => {\n";
            $js .= "    el.classList.add(...'{$escapedClasses}'.split(' '));\n";
            $js .= "  });\n\n";
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
require_once 'MCss.php';

$styleText = <<<EOT
/* Комментарии можно писать так и они будут удалены */
h1, h2, h3 {
  font-bold
  text-wrap:balance
}

.title, #header {
  text-lg
  text-primary
}

.btn {
  bg-blue-500
  hover:bg-blue-700
  text-white
  font-bold
  py-2
  px-4
  rounded
}

input[type=submit] {
  btn-submit
  cursor-pointer
}
EOT;

$mc = new MCss($styleText);
$script = $mc->generateScript();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Пример MCssJs</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <h1>Заголовок</h1>
  <h2>Подзаголовок</h2>
  <div class="title">Текст с классом title</div>
  <div id="header">Элемент с id header</div>
  <button class="btn">Кнопка</button>
  <input type="submit" value="Отправить" />
  
  <script>
    <?= $script ?>
  </script>
</body>
</html>
```

---

## Итоги

- Парсер читает CSS-подобный синтаксис с классами внутри фигурных скобок.
- Удаляет комментарии.
- Поддерживает селекторы тегов, классов, id, атрибутные селекторы.
- Генерирует JS, который динамически добавляет или заменяет классы.
- Очень удобный и привычный для глаз формат записи.

Если нужно, помогу с доработками или интеграцией!

