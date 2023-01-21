# LibCrackWrapper — binding for libcrack library
This library allows you to check the password strength

### Basic usage

```php
$checker = new \LibCrackWrapper\Wrapper();
$result = $checker->checkPassword($password);

if ($result->isStrongPassword()) {
     echo "Your password is good enough.\n";
} else {
     echo "This is a weak password. Reason: ", $result->getMessage(), "\n";
}
```
