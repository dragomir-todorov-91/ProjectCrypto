<?php

use lib\Config;

// DB Config
Config::write('db.host', 'localhost');
Config::write('db.port', '');
Config::write('db.basename', 'crypto_storage2tbl');
Config::write('db.user', 'project_crypto');
Config::write('db.password', 'crypto');

// Project Config
Config::write('path', 'http://localhost/slimMVC');