<?php
// Datoteka: htdocs/app/forms/log_out.php

require_once "config.php";
session_start();
session_destroy();
Util::redirect("/views/log_in.phtml");
