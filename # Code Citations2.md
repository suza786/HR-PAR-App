# Code Citations

## License: unknown
https://github.com/jbreemes/LaravelProject/blob/35a9c9a931115bcb7ce8db39e6f5244588fc9813/myproject/app/Http/Controllers/WorkLoginController.php

```
Verified! The Employee model is now correctly configured for `email` + `password` authentication.

✅ **Current Setup:**

- **Email Field**: Used as the unique identifier
- **Password Field**: Properly hashed with `bcrypt($value)` mutator
- **Guard**: `employee` configured with session driver
- **Provider**: `employees` pointing to Employee model

## How to Login:

```php
Auth::guard('employee')->attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

Or using the default guard (if set to employee):
```php
Auth::attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

## Example Login Controller Method:

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('employee')->attempt($credentials)) {
        $request->session()->regenerate();
        return
```


## License: unknown
https://github.com/jbreemes/LaravelProject/blob/35a9c9a931115bcb7ce8db39e6f5244588fc9813/myproject/app/Http/Controllers/WorkLoginController.php

```
Verified! The Employee model is now correctly configured for `email` + `password` authentication.

✅ **Current Setup:**

- **Email Field**: Used as the unique identifier
- **Password Field**: Properly hashed with `bcrypt($value)` mutator
- **Guard**: `employee` configured with session driver
- **Provider**: `employees` pointing to Employee model

## How to Login:

```php
Auth::guard('employee')->attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

Or using the default guard (if set to employee):
```php
Auth::attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

## Example Login Controller Method:

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('employee')->attempt($credentials)) {
        $request->session()->regenerate();
        return
```


## License: unknown
https://github.com/jbreemes/LaravelProject/blob/35a9c9a931115bcb7ce8db39e6f5244588fc9813/myproject/app/Http/Controllers/WorkLoginController.php

```
Verified! The Employee model is now correctly configured for `email` + `password` authentication.

✅ **Current Setup:**

- **Email Field**: Used as the unique identifier
- **Password Field**: Properly hashed with `bcrypt($value)` mutator
- **Guard**: `employee` configured with session driver
- **Provider**: `employees` pointing to Employee model

## How to Login:

```php
Auth::guard('employee')->attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

Or using the default guard (if set to employee):
```php
Auth::attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

## Example Login Controller Method:

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('employee')->attempt($credentials)) {
        $request->session()->regenerate();
        return
```


## License: unknown
https://github.com/jbreemes/LaravelProject/blob/35a9c9a931115bcb7ce8db39e6f5244588fc9813/myproject/app/Http/Controllers/WorkLoginController.php

```
Verified! The Employee model is now correctly configured for `email` + `password` authentication.

✅ **Current Setup:**

- **Email Field**: Used as the unique identifier
- **Password Field**: Properly hashed with `bcrypt($value)` mutator
- **Guard**: `employee` configured with session driver
- **Provider**: `employees` pointing to Employee model

## How to Login:

```php
Auth::guard('employee')->attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

Or using the default guard (if set to employee):
```php
Auth::attempt([
    'email' => $request->email,
    'password' => $request->password
]);
```

## Example Login Controller Method:

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('employee')->attempt($credentials)) {
        $request->session()->regenerate();
        return
```

