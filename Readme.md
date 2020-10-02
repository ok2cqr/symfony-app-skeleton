# Symfony 5.1.x app skeleton

It includes user registration, login and password reset function.

### Running Code checks
```bash
./code-check.bash
```

### Running yarn
```bash
symfony run yarn encore dev --watch
```

### Update translations
```bash
symfony console translation:update en --force --domain=messages
symfony console translation:update cs --force --domain=messages
```

### Migrations
```bash
symfony console make:migration
symfony console doctrine:migrations:migrate
```