<?php

exec("sensors|grep Core|awk '{print $3}'", $wendu1);
$r['cputemp'] = ($wendu1[0] + $wendu1[1]) / 2;
exec("hddtemp /dev/sda|awk '{print $4}'", $wendu2);
$r['hddtemp'] = $wendu2[0] + 0;
echo json_encode($r);
