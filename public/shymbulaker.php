<?php
session_start();
$shref = $_SESSION['shref'];
$_SESSION['shref'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
echo "<html>
<head></head>
<body>
<script type=\"text/javascript\"><!--
cz_user=52491;cz_type=1;cz_str=\"&wd=\"+screen.width;
cz_str+=\"&hg=\"+screen.height+\"&du=\"+escape(\"".$_SESSION['shref']."\");
cz_str+=\"&rf=\"+escape(\"$shref\");
cz_str=\"<img src='http://zero.kz/c.php?u=\"+cz_user+\"&t=\"+cz_type+cz_str+\"'\"+
\" width='88' height='31'/>\";
document.write(cz_str);//--></script>
</body>
</html>"; 
