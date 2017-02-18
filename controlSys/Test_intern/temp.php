<?php
echo file_get_contents("./temp.html");

echo "<form action='' method='POST'>";

echo "<input type='submit' name='zap16' value='Zapnout podlahovku'>";
echo "<input type='submit' name='zap19' value='Zapnout elektrokotel'>";
echo "<input type='submit' name='zap20' value='Zapnout obehove cerpadlo'>";

echo "<input type='submit' name='vyp16' value='Vypnout podlahovku'>";
echo "<input type='submit' name='vyp19' value='Vypnout elektrokotel'>";
echo "<input type='submit' name='vyp20' value='Vypnout obehove cerpadlo'>";

echo "<input type='submit' name='status16' value='Stav podlahovka'>";
echo "<input type='submit' name='status19' value='Stav elektrokotel'>";
echo "<input type='submit' name='status20' value='Stav obehove cerpadlo'>";

echo "</form>";
          
echo "<br>";
if (isset($_POST['zap16'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/zap16.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['zap19'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/zap19.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['zap20'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/zap20.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['vyp16'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/vyp16.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['vyp19'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/vyp19.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['vyp20'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/vyp20.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['status16'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/status16.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['status19'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/status19.py'));
	echo $out;
  echo "<br>";
}

if (isset($_POST['status20'])) {
	echo "<br>";
	$out = shell_exec(escapeshellcmd('sudo python /var/www/html/status20.py'));
	echo $out;
  echo "<br>";
}



echo "<br>";
echo "<br>";


