***DON'T FORGET TO CREATE .htaccess***
```
<FilesMatch ".phpw$">
Order Allow,Deny
Deny from all
</FilesMatch>
```

Write pretty code with PHPW!

Try PHPW now: http://prettybits.ru/phpw.php

`ClassName = {` -> `class ClassName {`

`_` -> `protected`

`__` -> `private`

`(($k,$v) in $items)` -> `foreach ($items as $k=>$v)`

`(($v) in $items)` -> `foreach ($items as $v)`

`(($k,) in $items)` -> `foreach (array_keys($items) as $k)`

`someName($params) {` -> `function someName($params) {`

`some line of code` -> `some line of code;`

`ClassName.method()` -> `ClassName::method()`

`$object.method()` -> `$object->method`

`$str1 # $str2` -> `$str1 . $str2`
