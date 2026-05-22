# Создание дистрибутива BIR

## Структура каталога

```
deploy/
├── bir-app-v1.0.9.tar.gz   # Текущий архив дистрибутива
└── AGENTS.md               # Этот файл
```

## Команда для создания дистрибутива

Выполнять из корня проекта (`/home/svv/DEV/BIR`):

```bash
# Очистка старого архива
rm -f deploy/bir-app-*.tar.gz

# Создание временной директории
mkdir -p .tmp/bir-app

# Копирование файлов (с исключениями)
rsync -av \
  --exclude=node_modules \
  --exclude=vendor \
  --exclude=.git \
  --exclude=tests \
  --exclude=.editorconfig \
  --exclude=.gitattributes \
  --exclude=phpunit.xml \
  --exclude=.phpunit.result.cache \
  --exclude=storage/app \
  --exclude=.env \
  app/ .tmp/bir-app/

# Удаление симлинков
cd .tmp/bir-app && find . -type l -delete

# Удаление ненужных .md файлов
find . -name "*.md" ! -name "README.md" ! -name "README_RU.md" -delete

# Создание архива без префикса bir-app (файлы в корне)
cd /home/svv/DEV/BIR
tar -czf deploy/bir-app-v1.0.X.tar.gz -C .tmp/bir-app .

# Очистка
rm -rf .tmp/bir-app
```

## Важные моменты

1. **Версия**: замените `v1.0.X` на актуальную версию в обоих местах (имя файла и внутри `public/install/index.php`)

2. **Структура архива**: файлы должны быть в корне архива, не в поддиректории

3. **Права**: после распаковки на сервере нужно выполнить:
   ```bash
   chmod -R 755 .
   chmod -R 777 storage bootstrap/cache public/uploads public/build public/install
   ```

4. **Проверка архива**:
   ```bash
   tar -tzf deploy/bir-app-v1.0.X.tar.gz | grep -E 'public/install/index.php|public/index.php'