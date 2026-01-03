{{--
 | 2026 DeveMain
 |
 | All rights reserved. For internal use only.
 | Unauthorized copying, modification, or distribution is prohibited.
 |
 | @author    DeveMain <devemain@gmail.com>
 | @copyright 2026 DeveMain
 | @license   PROPRIETARY
 | @link      https://github.com/DeveMain
 --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test page</title>
</head>
<body>
<h1>Test page</h1>

<h2>PHP version:</h2>
<p style="color: green;">
    {{ PHP_VERSION }}
</p>

<h2>Permissions Check:</h2>
@foreach($paths as $path => $writable)
    <p style="color: {{ $writable ? 'green' : 'red' }}">
        {{ $path }}: {{ $writable ? 'WRITABLE ✅' : 'NOT WRITABLE ❌' }}
    </p>
@endforeach
</body>
</html>
