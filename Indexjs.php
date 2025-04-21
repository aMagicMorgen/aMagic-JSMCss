<?php
require_once 'MCssJS.php';

$styleString = <<<EOT
!h1 .title #header | font-bold text-wrap:balance
!.btn | bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
!#special | border-2 border-red-500 p-4
EOT;

$jsManager = new MCssJS($styleString);
$script = $jsManager->generateScript();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Пример JSMCss</title>
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
