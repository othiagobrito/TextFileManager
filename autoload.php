<?php

spl_autoload_register(fn ($className) => include "classes/{$className}.php");
