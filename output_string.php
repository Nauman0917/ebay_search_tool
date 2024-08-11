<?php

    $output_file_name = 'output_string_temp.html';

    if(!file_exists($output_file_name)){
        exit('Loading...');
    }

    echo file_get_contents($output_file_name);

    echo "\n".'</table>';