<?php

chdir('\dev');
$myCommand = "soffice -p test_doc.docx";
shell_exec($myCommand);
