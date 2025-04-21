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

**Лицензия:** MIT  
**Автор:** Ваше имя/команда  
**Версия:** 1.0.0
