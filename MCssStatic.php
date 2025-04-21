<?php

/**
 * Класс MCssStatic — статический менеджер CSS-классов.
 * 
 * На основе текстовых правил и исходного HTML добавляет/заменяет классы в HTML-разметке.
 * Не использует JS, все классы подставляются на сервере.
 */
class MCss
{
    private $stylesMap = [];
    private $replaceSelectors = [];
    private $idSelectors = [];

    /**
     * Конструктор парсит правила в формате JSMCss (!селекторы | классы)
     * @param string $styleString
     */
    public function __construct(string $styleString)
    {
        $this->parseStyleString($styleString);
    }

    private function parseStyleString(string $styleString): void
    {
        $blocks = preg_split('/\!/', trim($styleString));
        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') continue;

            $parts = preg_split('/\|/', $block, 2);
            if (count($parts) < 2) continue;

            $selectorsPart = trim($parts[0]);
            $classesPart = preg_replace('/\s+/', ' ', trim($parts[1]));

            $selectors = preg_split('/\s+/', $selectorsPart);

            foreach ($selectors as $selector) {
                if ($selector === '') continue;

                if (strpos($selector, '#') === 0) {
                    $id = substr($selector, 1);
                    if (!isset($this->idSelectors[$id])) {
                        $this->idSelectors[$id] = [];
                    }
                    $this->idSelectors[$id][] = $classesPart;
                } elseif (strpos($selector, '.') === 0) {
                    if (!isset($this->replaceSelectors[$selector])) {
                        $this->replaceSelectors[$selector] = '';
                    }
                    if ($this->replaceSelectors[$selector] !== '') {
                        $this->replaceSelectors[$selector] .= ' ';
                    }
                    $this->replaceSelectors[$selector] .= $classesPart;
                } else {
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
     * Обрабатывает HTML, добавляя и заменяя классы согласно правилам.
     * @param string $html Исходный HTML
     * @return string Обработанный HTML с классами
     */
    public function applyStyles(string $html): string
    {
        $dom = new DOMDocument();
        // Подавляем предупреждения из-за невалидного HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Добавляем классы по тегам (stylesMap)
        foreach ($this->stylesMap as $tag => $classes) {
            $nodes = $xpath->query('//' . $tag);
            foreach ($nodes as $node) {
                $this->addClassesToNode($node, $classes);
            }
        }

        // Заменяем классы у элементов с классами (replaceSelectors)
        foreach ($this->replaceSelectors as $selector => $classes) {
            $className = substr($selector, 1);
            $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' {$className} ')]");
            foreach ($nodes as $node) {
                $this->replaceClassInNode($node, $className, $classes);
            }
        }

        // Добавляем классы по id (idSelectors)
        foreach ($this->idSelectors as $id => $classesList) {
            $node = $dom->getElementById($id);
            if ($node) {
                foreach ($classesList as $classes) {
                    $this->addClassesToNode($node, $classes);
                }
            }
        }

        // Возвращаем обработанный HTML без DOCTYPE и html/body тегов
        $body = $dom->getElementsByTagName('body')->item(0);
        $innerHTML = '';
        foreach ($body->childNodes as $child) {
            $innerHTML .= $dom->saveHTML($child);
        }
        return $innerHTML;
    }

    private function addClassesToNode(DOMElement $node, string $classes): void
    {
        $existing = $node->getAttribute('class');
        $existingClasses = preg_split('/\s+/', trim($existing));
        $newClasses = preg_split('/\s+/', trim($classes));
        $allClasses = array_unique(array_merge($existingClasses, $newClasses));
        $node->setAttribute('class', implode(' ', array_filter($allClasses)));
    }

    private function replaceClassInNode(DOMElement $node, string $oldClass, string $newClasses): void
    {
        $existing = $node->getAttribute('class');
        $classes = preg_split('/\s+/', trim($existing));
        $classes = array_filter($classes, fn($c) => $c !== $oldClass);
        $newClassesArr = preg_split('/\s+/', trim($newClasses));
        $allClasses = array_unique(array_merge($classes, $newClassesArr));
        $node->setAttribute('class', implode(' ', $allClasses));
    }
}
