<?php
    require __DIR__ . '/../vendor/autoload.php';
?>
<html>
<head>

</head>
<body>
    <h1>SPINEN Simple PHP Web Tester</h1>
    <p>The time currently is: <?php echo Carbon\Carbon::now(); ?></p>
    <h2>$_GET vars</h2>
    <pre><?php var_export($_GET); ?></pre>
</body>
</html>

