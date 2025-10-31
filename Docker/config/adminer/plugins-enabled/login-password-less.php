<?php
require_once('plugins/login-password-less.php');
return new AdminerLoginPasswordLess(password_hash("*", PASSWORD_DEFAULT));
