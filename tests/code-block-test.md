```php
public function mimetype(): string
{
  if (! $mimetype) {
    return 'text/plain';

  } else {
    $this->mimetype = $mimetype;

  }
  return $this->mimetype;
}
```
