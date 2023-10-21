<?php
//  Save the output as manual cache
$data=implode('',$output);
$file = fopen ( $cache_filename, 'w' );
fwrite ( $file, $data );
fclose ( $file );

print $data;