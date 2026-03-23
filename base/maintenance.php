<?php
    if (  true && ! in_array($_SERVER['REMOTE_ADDR'],
           [/*'192.168.1.254.',*/]
        )) {
?><!DOCTYPE html>
<html lang='fr'>

<head>
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <meta name='Author' content='Olivier Cardoso'>
    <meta charset='UTF-8'>
    <meta name='copyright' content='&copy; 2000-2019 Olivier Cardoso.'>
    <meta name='keywords' content='TDS'>
    <meta name='date' content='1994-11-06T08:49:37+00:00'>
    <title>Foire aux enseignements</title>
    <link rel="icon" href="/favicon.ico" />
    <style>
        body {
            font: normal 13px/20px Arial, Helvetica, sans-serif;
            color: #969696;
            margin: 0em;
            background-color: #fff;
        }

        .error-center {
            margin: 0;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .error-404 {
            margin: 0em;
            font-size: 7em;
            line-height: 1em;
            font-weight: bold;
        }

        .error-404 span {
            text-shadow: 1px 5px 7px rgba(150, 150, 150, 1);
        }


        h1,
        h2 {
            color: #616161;
        }

        h2 {
            font-size: 2em;
            line-height: 2em;
        }
    </style>

</head>

<body id="top">

    <div class="error-center">
        <h2>Tableau de service de <?php echo \TDS\App::$appName; ?></h2>
        <h1 class="error-404">
            <span>Maintenance</span>
        </h1>

        <h2 class="title">Opération de maintenance en cours...</h2>
        <p class="message">
            Patience, cela devrait revenir rapidement...
            <br>
            ou pas !
        </p>

    </div>

</body>

</html>
<?php
    exit();
}
