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
