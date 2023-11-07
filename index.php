<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Touiteur</title>
    <link rel="stylesheet" type="text/css" href="css/index_style.css">
</head>

<body>
    <?php
        require_once 'vendor/autoload.php';

        use iutnc\touiteur\dispatch\Dispatcher;
        use iutnc\touiteur\bd\ConnectionFactory;

        $disp = new Dispatcher();
        $disp->run();
    ?>

</body>