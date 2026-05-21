<?php

return array (
  '_meta' => 
  array (
    'code' => 'ru',
    'name' => 'Russian',
    'native_name' => 'Русский',
    'is_active' => true,
    'is_default' => false,
  ),
  'translations' => 
  array (
    'brand' => 'IPP',
    'meta' => 
    array (
      'description' => 'Бесплатный онлайн-инструмент для пакетного изменения размера, конвертации и обработки изображений',
    ),
    'nav' => 
    array (
      'tools' => 'Инструменты',
      'plans' => 'Тарифы',
      'dashboard' => 'Кабинет',
      'login' => 'Войти',
      'register' => 'Регистрация',
      'logout' => 'Выйти',
      'open_menu' => 'Открыть меню',
    ),
    'lang' => 
    array (
      'label' => 'Язык',
    ),
    'footer' => 
    array (
      'copyright' => 'Онлайн обработка изображений',
      'ttl' => 'Файлы хранятся :hours часов и автоматически удаляются',
    ),
    'common' => 
    array (
      'save' => 'Сохранить',
      'delete' => 'Удалить',
      'actions' => 'Действия',
      'reset' => 'Сбросить',
      'filter' => 'Фильтровать',
      'search' => 'Найти',
      'all' => 'Все',
      'yes' => 'Да',
      'no' => 'Нет',
      'guest' => 'гость',
      'clear_all' => 'Очистить всё',
      'cancel' => 'Отмена',
      'back' => 'Назад',
    ),
    'roles' => 
    array (
      'user' => 'Пользователь',
      'admin' => 'Администратор',
      'superadmin' => 'Суперадмин',
    ),
    'status' => 
    array (
      'job' => 
      array (
        'pending' => 'В очереди',
        'processing' => 'Обработка',
        'done' => 'Готово',
        'failed' => 'Ошибка',
        'expired' => 'Истёк срок',
      ),
    ),
    'auth' => 
    array (
      'login_title' => 'Вход',
      'register_title' => 'Регистрация',
      'email' => 'Email',
      'password' => 'Пароль',
      'password_confirm' => 'Подтвердите пароль',
      'remember' => 'Запомнить меня',
      'login_btn' => 'Войти',
      'register_btn' => 'Зарегистрироваться',
      'wrong_credentials' => 'Неверный email или пароль.',
      'welcome' => 'Добро пожаловать!',
      'name' => 'Имя',
      'name_placeholder' => 'Ваше имя',
    ),
    'dashboard' => 
    array (
      'admin_access' => 
      array (
        'title' => 'Административный доступ',
        'description' => 'У вас есть доступ к панели администратора для управления пользователями, заданиями и тарифами.',
        'action' => 'Перейти в админку',
      ),
      'current_plan' => 'Текущий тариф',
      'jobs_today' => 'Заданий сегодня',
      'credits' => 'Кредиты',
      'recent_jobs' => 'Последние задания',
      'job_fallback_name' => 'Задание #:uuid',
      'no_jobs' => 'Пока нет заданий.',
    ),
    'history' => 
    array (
      'title' => 'История заданий',
      'status' => 'Статус',
      'files' => 'Файлы',
      'date' => 'Дата',
      'actions' => 'Действия',
      'open' => 'Открыть',
      'empty' => 'Пока нет заданий',
    ),
    'job' => 
    array (
      'title' => 'Задание #:uuid',
      'new_job' => 'Новое задание',
      'processing_title' => 'Обработка изображений',
      'files_label' => 'Файлов',
      'processed' => 'Обработано',
      'errors' => 'Ошибок',
      'pending_message' => 'Задание в очереди, ожидайте...',
      'download_zip' => 'Скачать ZIP',
      'archive_ttl' => 'Архив будет доступен :hours часов',
      'processed_files' => 'Обработанные файлы',
      'failed_message' => 'Обработка завершилась с ошибками. Проверьте список файлов ниже.',
      'files_section' => 'Файлы',
    ),
    'presets' => 
    array (
      'title' => 'Пресеты',
      'name' => 'Название',
      'save' => 'Сохранить',
      'delete' => 'Удалить',
      'empty' => 'Пока нет пресетов.',
      'messages' => 
      array (
        'pipeline_json' => 'Pipeline должен быть валидным JSON массивом.',
        'pipeline_array' => 'Pipeline должен быть массивом шагов.',
        'saved' => 'Пресет сохранён.',
        'deleted' => 'Пресет удалён.',
      ),
    ),
    'plans' => 
    array (
      'popular' => 'Популярный',
      'max_files' => 'Файлов за раз',
      'max_file_size' => 'Размер файла, до',
      'daily_limit' => 'Лимит в день',
      'ai_credits' => 'AI кредиты/мес',
      'price' => 
      array (
        'free' => 'Бесплатно',
        'month_short' => 'мес',
      ),
    ),
    'jobs' => 
    array (
      'messages' => 
      array (
        'created' => 'Задание создано и поставлено в очередь.',
        'daily_limit_reached' => 'Вы достигли дневного лимита заданий (:limit/день). Обновите тариф.',
        'max_files_exceeded' => 'Максимум :max файлов за раз на вашем тарифе.',
        'file_too_large' => 'Файл превышает лимит :limit MB.',
        'output_format_unavailable' => 'Выбранный формат недоступен на вашем тарифе.',
        'watermark_unavailable' => 'Функция watermark недоступна на вашем тарифе.',
        'operation_unavailable' => 'Операция :operation недоступна на вашем тарифе.',
        'archive_not_ready' => 'Архив ещё не готов.',
        'archive_missing' => 'Архив не найден или истёк срок хранения.',
        'filter_validation' => 
        array (
          'brightness' => 'Яркость должна быть от -100 до 100.',
          'contrast' => 'Контраст должен быть от -100 до 100.',
          'saturation' => 'Насыщенность должна быть от 0 до 200.',
          'blur' => 'Размытие должно быть от 0 до 20.',
          'sepia' => 'Сепия должна быть от 0 до 100.',
          'grayscale' => 'Оттенки серого должны быть от 0 до 100.',
          'hue_rotate' => 'Поворот оттенка должен быть от 0 до 360.',
        ),
      ),
    ),
    'api' => 
    array (
      'access_denied' => 'API недоступно на вашем текущем тарифе.',
    ),
    'admin' => 
    array (
      'common' => 
      array (
        'title' => 'Админка',
      ),
      'nav' => 
      array (
        'overview' => 'Обзор',
        'users' => 'Пользователи',
        'jobs' => 'Задания',
        'plans' => 'Тарифы',
        'localization' => 'Локализация',
        'statistics' => 'Статистика',
      ),
      'dashboard' => 
      array (
        'subtitle' => 'Базовый обзор состояния платформы и быстрые действия.',
        'stats' => 
        array (
          'users' => 'Пользователи',
          'plans' => 'Тарифы',
          'jobs_today' => 'Заданий сегодня',
          'jobs_processing' => 'В очереди / обработке',
          'expired_archives' => 'Просроченные архивы',
          'stale_jobs' => 'Потенциально зависшие задания',
        ),
        'quick_actions' => 
        array (
          'title' => 'Быстрые действия',
          'cleanup' => 'Очистить просроченные архивы',
          'stop_stale' => 'Остановить зависшие задания',
          'stale_threshold' => 'Порог зависшего задания: старше :time.',
        ),
        'available_now' => 
        array (
          'title' => 'Что уже есть',
          'cleanup' => 'Очистка невостребованных архивов и результирующих файлов.',
          'stop_stale' => 'Ручная остановка зависших pending/processing заданий.',
          'plan_editing' => 'Редактирование тарифов без отдельного конструктора платформы.',
        ),
        'recent_jobs' => 
        array (
          'title' => 'Последние задания',
          'user' => 'Пользователь',
          'status' => 'Статус',
          'files' => 'Файлы',
          'created' => 'Создано',
          'empty' => 'Заданий пока нет.',
        ),
      ),
      'users' => 
      array (
        'title' => 'Админка — пользователи',
        'subtitle' => 'Базовое управление пользователями, ролями, тарифом и кредитами.',
        'search_placeholder' => 'Поиск по имени или email',
        'registered' => 'Зарегистрирован',
        'plan' => 'Тариф',
        'no_plan' => 'Без тарифа',
        'roles' => 'Роли',
        'effective_plan' => 'Эффективный тариф',
        'unlimited_access' => 'Безлимитный доступ',
        'empty' => 'Пользователи не найдены.',
        'messages' => 
        array (
          'created' => 'Пользователь :email создан.',
          'updated' => 'Пользователь :email обновлён.',
          'deleted' => 'Пользователь :email удалён.',
          'password_reset' => 'Пароль для :email изменён.',
          'cannot_delete_self' => 'Нельзя удалить самого себя.',
        ),
        'create_title' => 'Добавить пользователя',
        'create_subtitle' => 'Создание нового пользователя вручную',
        'password_min' => 'Минимум 8 символов',
        'new_password' => 'Новый пароль',
        'confirm_delete' => 'Удалить пользователя :email? Все его данные будут удалены безвозвратно.',
        'total_jobs' => 'Всего заданий',
        'blocking' => 
        array (
          'title' => 'Блокировка',
          'permanent' => 'Постоянная блокировка',
          'until' => 'Заблокирован до',
          'reason' => 'Причина',
          'reason_placeholder' => 'Укажите причину блокировки',
        ),
        'status' => 
        array (
          'blocked_permanent' => 'Заблокирован',
          'blocked_until' => 'Заблокирован до :until',
        ),
        'fields' => 
        array (
          'unlimited_access' => 'Безлимитный доступ',
        ),
        'actions' => 
        array (
          'create' => 'Добавить пользователя',
          'reset_password' => 'Сбросить пароль',
          'reset_password_confirm' => 'Изменить пароль',
          'stats' => 'Статистика',
        ),
        'stats_title' => 'Статистика пользователя',
        'stats' => 
        array (
          'total_jobs' => 'Всего заданий',
          'jobs_today' => 'Сегодня',
          'jobs_this_week' => 'На этой неделе',
          'jobs_this_month' => 'В этом месяце',
          'activity_chart' => 'График активности',
          'recent_jobs' => 'Последние задания',
          'no_jobs' => 'Нет заданий',
          'jobs_per_day' => 'Заданий в день',
        ),
      ),
      'jobs' => 
      array (
        'title' => 'Админка — задания',
        'subtitle' => 'Фильтрация, сортировка, поштучное и массовое удаление, очистка просроченных архивов и ручная остановка зависших заданий.',
        'filters' => 
        array (
          'status' => 'Статус',
          'expired_only' => 'Только просроченные',
          'stale_only' => 'Только зависшие',
          'sort' => 'Сортировка',
          'sort_created' => 'По созданию',
          'sort_expires' => 'По истечению',
          'sort_status' => 'По статусу',
          'direction' => 'Порядок',
          'direction_desc' => 'Сначала новые',
          'direction_asc' => 'Сначала старые',
        ),
        'actions' => 
        array (
          'cleanup' => 'Очистить просроченные архивы',
          'stop_stale' => 'Остановить зависшие задания',
        ),
        'stale_hint' => 'Зависшие = pending/processing старше :time.',
        'bulk' => 
        array (
          'select_all' => 'Выбрать всё на странице',
          'selected' => 'Выбрано',
          'delete' => 'Удалить выбранные',
        ),
        'table' => 
        array (
          'user' => 'Пользователь',
          'status' => 'Статус',
          'archive' => 'Архив',
          'expires' => 'Истекает',
          'created' => 'Создано',
          'actions' => 'Действия',
        ),
        'confirm' => 
        array (
          'bulk_delete' => 'Удалить выбранные задания? Это удалит архивы, исходники и результаты.',
          'delete_single' => 'Удалить это задание? Будут удалены архив, исходники и результаты.',
        ),
        'empty' => 'Ничего не найдено.',
        'messages' => 
        array (
          'cleaned' => 'Очищено просроченных архивов/результатов: :count.',
          'marked_stale_file' => 'Задание помечено администратором как зависшее.',
          'stopped_stale' => 'Помечено как зависшие и остановлено: :count заданий.',
          'deleted' => 'Задание :uuid удалено.',
          'bulk_deleted' => 'Удалено заданий: :count.',
        ),
      ),
      'plans' => 
      array (
        'title' => 'Админка — тарифы',
        'subtitle' => 'Управление тарифами и базовая аналитика по использованию.',
        'create_title' => 'Добавить тариф',
        'translation_locale' => 'Локаль перевода',
        'translation_hint' => 'Переводы тарифа редактируются прямо здесь для выбранного языка.',
        'fields' => 
        array (
          'name' => 'Название',
          'description' => 'Описание',
          'price_month' => 'Цена / мес',
          'price_year' => 'Цена / год',
          'currency' => 'Валюта',
          'max_files' => 'Файлов за раз',
          'max_file_size' => 'Размер файла, MB',
          'daily_limit' => 'Лимит в день',
          'monthly_credits' => 'Кредиты / мес',
          'storage_ttl_hours' => 'Срок хранения, ч',
          'sort_order' => 'Порядок',
          'allowed_formats' => 'Разрешённые форматы',
          'allowed_operations' => 'Разрешённые операции',
          'feature_flags' => 'Опции тарифа',
        ),
        'actions' => 
        array (
          'create' => 'Добавить тариф',
        ),
        'analytics' => 
        array (
          'users' => 'пользователей',
          'subscriptions' => 'Подписок',
          'jobs' => 'Заданий',
        ),
        'features' => 
        array (
          'watermark' => 'Watermark',
          'api_access' => 'API access',
          'priority_queue' => 'Priority queue',
          'ai_features' => 'ИИ-функции',
          'is_active' => 'Активен',
          'is_popular' => 'Популярный',
        ),
        'confirm_delete' => 'Удалить тариф :name?',
        'messages' => 
        array (
          'created' => 'Тариф добавлен.',
          'updated' => 'Тариф :name обновлён.',
          'deleted' => 'Тариф :name удалён.',
          'delete_blocked' => 'Нельзя удалить тариф: есть пользователи или подписки.',
        ),
      ),
      'localization' => 
      array (
        'title' => 'Админка — локализация',
        'subtitle' => 'Управление языками интерфейса, UI-переводами и локализованным контентом тарифов.',
        'add_locale' => 'Добавить язык',
        'existing_locales' => 'Существующие языки',
        'ui_translations' => 'UI-переводы',
        'search_placeholder' => 'Фильтр по ключу или тексту',
        'source_locale' => 'Исходный язык',
        'editing_locale' => 'Редактируемый язык',
        'saving' => 'Сохранение...',
        'saved' => 'Сохранено',
        'table_hint' => 'Строк в таблице: :count',
        'source_text' => 'Исходный текст',
        'translation_text' => 'Перевод',
        'state' => 'Источник',
        'add_single_translation' => 'Добавить отдельный перевод вручную',
        'optional_helper' => 'опционально',
        'code' => 'Код',
        'name' => 'Название',
        'native_name' => 'Native name',
        'is_active' => 'Активен',
        'is_default' => 'По умолчанию',
        'group' => 'Группа',
        'key' => 'Ключ',
        'locale' => 'Язык',
        'value' => 'Значение',
        'empty_translations' => 'Пока нет UI-переводов в БД.',
        'confirm_delete_locale' => 'Удалить язык :code? Переводы этой локали из БД будут удалены.',
        'actions' => 
        array (
          'add_locale' => 'Добавить язык',
          'make_default' => 'Сделать по умолчанию',
          'save_locale' => 'Сохранить язык',
          'save_translation' => 'Сохранить UI-перевод',
          'save_translations' => 'Сохранить переводы',
        ),
        'messages' => 
        array (
          'locale_created' => 'Локаль добавлена.',
          'locale_updated' => 'Локаль обновлена.',
          'locale_deleted' => 'Локаль удалена.',
          'locale_delete_default_blocked' => 'Нельзя удалить локаль по умолчанию.',
          'translation_saved' => 'UI-перевод сохранён.',
          'translations_updated' => 'Переводы обновлены.',
        ),
      ),
      'statistics' => 
      array (
        'title' => 'Статистика',
        'subtitle' => 'Общая статистика платформы',
        'period' => 
        array (
          'week' => 'Неделя',
          'month' => 'Месяц',
          'quarter' => 'Квартал',
        ),
        'today' => 'сегодня',
        'total_users' => 'Всего пользователей',
        'total_jobs' => 'Всего заданий',
        'active_subscriptions' => 'Активных подписок',
        'total_revenue' => 'Общий доход',
        'users_growth' => 'Рост пользователей',
        'jobs_activity' => 'Активность заданий',
        'revenue' => 'Доход',
        'top_users' => 'Топ пользователей',
        'jobs_count' => 'Заданий',
        'new_users' => 'Новые пользователи',
        'jobs_per_day' => 'Заданий в день',
        'daily_revenue' => 'Дневной доход',
        'no_data' => 'Нет данных',
      ),
    ),
    'app' => 
    array (
      'title' => 'Пакетная обработка изображений онлайн',
      'hero' => 
      array (
        'title' => 'Пакетная обработка изображений',
        'description' => 'Загружайте изображения, меняйте формат, размер и качество. Обрабатывайте десятки файлов за один запуск и скачивайте готовый ZIP.',
      ),
      'steps' => 
      array (
        'upload' => 'Загрузите файлы',
        'configure' => 'Настройте инструменты',
        'download' => 'Скачайте результат',
      ),
      'submit_failed' => 'Не удалось отправить форму',
      'upload_zone_aria' => 'Зона загрузки изображений',
      'upload_zone_title' => 'Загрузите изображения',
      'upload_zone_hint' => 'Перетащите файлы сюда или <span class="text-violet-400 underline">выберите</span>',
      'upload_zone_formats' => 'JPG, PNG, WebP, GIF, BMP, TIFF — до :size MB каждый',
      'more_files' => 
      array (
        'title' => 'Можно добавить файлы из другой папки',
        'description' => 'Нажмите на область загрузки или перетащите новые файлы',
      ),
      'uploaded_files' => 'Загруженные файлы',
      'sections' => 
      array (
        'format_quality' => 'Формат и качество',
        'resize' => 'Изменение размера',
        'rotate' => 'Поворот',
        'flip' => 'Отражение',
        'rename' => 'Переименование',
        'filters' => 'Фильтры изображений',
      ),
      'quality' => 'Качество (JPEG/WebP)',
      'resize' => 
      array (
        'none' => '— Не изменять —',
        'width' => 'По ширине',
        'height' => 'По высоте',
        'fixed' => 'Точный размер (px)',
        'fit' => 'Вписать в рамку',
        'cover' => 'Заполнить рамку (обрезка)',
      ),
      'rotate' => 
      array (
        'none' => '—',
        'left' => '↺ 90°',
        'flip' => '↕ 180°',
        'right' => '↻ 90°',
      ),
      'flip' => 
      array (
        'none' => '—',
        'horizontal' => '⇆ Горизонт.',
        'vertical' => '⇅ Вертикал.',
      ),
      'width_px' => 'Ширина (px)',
      'height_px' => 'Высота (px)',
      'allow_upscale' => 'Разрешить увеличение',
      'rename' => 
      array (
        'mode' => 'Режим имени',
        'original' => 'Сохранить исходное имя',
        'sequence' => 'Нумерация',
        'prefix' => 'Префикс',
        'suffix' => 'Суффикс',
        'start_number' => 'Нумерация с',
        'sequence_hint' => 'Для режима «Нумерация»: pic001_img.jpg, pic002_img.jpg и т.д.',
      ),
      'process' => 'Обработать',
      'uploading' => 'Загрузка файлов...',
    ),
    'js' => 
    array (
      'upload' => 
      array (
        'add_file' => 'Добавьте хотя бы один файл.',
        'form_not_found' => 'Не удалось найти форму отправки.',
        'redirect_missing' => 'Форма отправлена, но адрес результата не получен.',
        'submit_error' => 'Ошибка отправки формы. Попробуйте ещё раз.',
      ),
      'localization' => 
      array (
        'row_save_error' => 'Не удалось сохранить строку.',
      ),
      'job' => 
      array (
        'status' => 
        array (
          'pending' => 'В очереди',
          'processing' => 'Обработка',
          'done' => 'Готово',
          'failed' => 'Ошибка',
          'expired' => 'Истёк срок',
        ),
      ),
    ),
    'tools' => 
    array (
      'meta' => 
      array (
        'title' => 'Онлайн Инструменты для Изображений',
        'index_title' => 'Инструменты для Изображений — Бесплатные Онлайн Редакторы',
      ),
      'index' => 
      array (
        'title' => 'Инструменты для изображений',
        'subtitle' => 'Бесплатные онлайн инструменты для мгновенного редактирования и преобразования ваших изображений',
      ),
      'category' => 
      array (
        'basic' => 'Базовые инструменты',
        'filters' => 'Фильтры и эффекты',
      ),
      'features' => 
      array (
        'dragdrop' => 
        array (
          'title' => 'Перетащите',
          'desc' => 'Просто перетащите изображение для начала редактирования',
        ),
        'preview' => 
        array (
          'title' => 'Предпросмотр',
          'desc' => 'Мгновенно видьте изменения перед скачиванием',
        ),
        'download' => 
        array (
          'title' => 'Мгновенное скачивание',
          'desc' => 'Получите обработанное изображение за секунды',
        ),
      ),
      'back_to_tools' => 'Ко всем инструментам',
      'upload' => 
      array (
        'title' => 'Загрузить изображение',
        'drag_drop' => 'Перетащите изображение сюда',
        'or_click' => 'или нажмите для выбора',
        'processing' => 'Обработка...',
        'complete' => 'Готово!',
      ),
      'preview' => 
      array (
        'title' => 'Предпросмотр',
        'empty' => 'Загрузите изображение для предпросмотра',
        'hold_to_compare' => 'Удерживайте для сравнения',
        'download' => 'Скачать',
      ),
      'errors' => 
      array (
        'not_image' => 'Пожалуйста, загрузите изображение',
        'too_large' => 'Размер файла превышает 10 МБ',
        'upload_failed' => 'Ошибка загрузки, попробуйте снова',
        'processing_failed' => 'Ошибка обработки изображения',
      ),
      'crop' => 
      array (
        'name' => 'Обрезка',
        'description' => 'Обрежьте изображения до любого размера или соотношения сторон',
        'settings' => 'Настройки обрезки',
        'width' => 'Ширина (px)',
        'height' => 'Высота (px)',
        'position_x' => 'Позиция X',
        'position_y' => 'Позиция Y',
      ),
      'rotate' => 
      array (
        'name' => 'Поворот',
        'description' => 'Поворачивайте изображения на любой угол',
        'settings' => 'Настройки поворота',
        'angle' => 'Угол поворота',
      ),
      'flip' => 
      array (
        'name' => 'Отражение',
        'description' => 'Отражайте изображения по горизонтали или вертикали',
        'settings' => 'Настройки отражения',
        'direction' => 'Направление отражения',
        'horizontal' => 'Горизонтально',
        'vertical' => 'Вертикально',
      ),
      'resize' => 
      array (
        'name' => 'Изменение размера',
        'description' => 'Изменяйте размер изображений до точных размеров',
        'settings' => 'Настройки размера',
        'width' => 'Ширина (px)',
        'height' => 'Высота (px)',
        'maintain_aspect' => 'Сохранить соотношение сторон',
        'presets' => 'Быстрые пресеты:',
      ),
      'watermark' => 
      array (
        'name' => 'Добавить водяной знак',
        'description' => 'Добавляйте текстовый или графический водяной знак с настройкой позиции, размера и прозрачности',
        'settings' => 'Настройки водяного знака',
        'type' => 'Тип водяного знака',
        'type_text' => 'Текстовый',
        'type_image' => 'Графический',
        'text' => 'Текст водяного знака',
        'color' => 'Цвет текста',
        'upload_logo' => 'Загрузить логотип',
        'upload_logo_hint' => 'Лучше всего подходит PNG с прозрачностью',
        'position' => 'Позиция',
        'top_left' => 'Сверху слева',
        'top_right' => 'Сверху справа',
        'bottom_left' => 'Снизу слева',
        'bottom_right' => 'Снизу справа',
        'center' => 'По центру',
        'size' => 'Размер',
        'opacity' => 'Прозрачность',
        'offset_x' => 'Смещение X',
        'offset_y' => 'Смещение Y',
      ),
      'annotate' => 
      array (
        'name' => 'Аннотация изображения',
        'description' => 'Рисуйте линии, прямоугольники и стрелки поверх изображения',
        'settings' => 'Настройки аннотации',
        'type' => 'Тип фигуры',
        'line' => 'Линия',
        'rectangle' => 'Прямоугольник',
        'arrow' => 'Стрелка',
        'color' => 'Цвет',
        'thickness' => 'Толщина',
        'opacity' => 'Прозрачность',
        'diagonal_1' => 'Диагональ 1',
        'diagonal_2' => 'Диагональ 2',
      ),
      'frame' => 
      array (
        'name' => 'Добавить рамку',
        'description' => 'Добавляйте сплошную, двойную или пунктирную рамку вокруг изображения',
        'settings' => 'Настройки рамки',
        'style' => 'Стиль рамки',
        'solid' => 'Сплошная',
        'double' => 'Двойная',
        'dashed' => 'Пунктирная',
        'color' => 'Цвет рамки',
        'thickness' => 'Толщина рамки',
      ),
      'enlarge' => 
      array (
        'name' => 'Увеличить изображение',
        'description' => 'Увеличивайте изображения с интерполяцией и сохранением качества',
        'settings' => 'Настройки увеличения',
        'scale' => 'Масштаб',
        'original' => 'Оригинал',
        'result' => 'Результат',
      ),
      'brightness' => 
      array (
        'name' => 'Яркость',
        'description' => 'Регулируйте яркость изображений',
        'settings' => 'Настройки яркости',
        'value' => 'Яркость',
      ),
      'contrast' => 
      array (
        'name' => 'Контраст',
        'description' => 'Регулируйте контраст изображений',
        'settings' => 'Настройки контраста',
        'value' => 'Контраст',
      ),
      'saturation' => 
      array (
        'name' => 'Насыщенность',
        'description' => 'Регулируйте насыщенность изображений',
        'settings' => 'Настройки насыщенности',
        'value' => 'Насыщенность',
      ),
      'exposure' => 
      array (
        'name' => 'Экспозиция',
        'description' => 'Управляйте экспозицией изображений',
        'settings' => 'Настройки экспозиции',
        'value' => 'Экспозиция',
      ),
      'temperature' => 
      array (
        'name' => 'Температура',
        'description' => 'Изменяйте цветовую температуру изображений',
        'settings' => 'Настройки температуры',
        'value' => 'Температура',
        'cool' => 'Холодный',
        'warm' => 'Тёплый',
      ),
      'gamma' => 
      array (
        'name' => 'Гамма',
        'description' => 'Тонко настройте уровни гаммы в изображениях',
        'settings' => 'Настройки гаммы',
        'value' => 'Гамма',
      ),
      'clarity' => 
      array (
        'name' => 'Чёткость',
        'description' => 'Улучшайте чёткость изображений',
        'settings' => 'Настройки чёткости',
        'value' => 'Чёткость',
        'soft' => 'Мягкий',
        'sharp' => 'Резкий',
      ),
      'vibrance' => 
      array (
        'name' => 'Вибрация',
        'description' => 'Усиливайте приглушенные цвета, сохраняя естественные оттенки изображения',
        'settings' => 'Настройки вибрации',
        'value' => 'Вибрация',
      ),
      'blur' => 
      array (
        'name' => 'Размытие',
        'description' => 'Применяйте контролируемое размытие к изображению',
        'settings' => 'Настройки размытия',
        'level' => 'Степень размытия',
      ),
      'filters' => 
      array (
        'name' => 'Фото фильтры',
        'description' => 'Применяйте профессиональные фото фильтры и эффекты',
        'settings' => 'Настройки фильтров',
        'brightness' => 'Яркость',
        'contrast' => 'Контраст',
        'blur' => 'Размытие',
        'grayscale' => 'Чёрно-белое',
        'darken' => 'Темнее',
        'lighten' => 'Светлее',
        'low' => 'Низкий',
        'high' => 'Высокий',
        'light' => 'Лёгкий',
        'medium' => 'Средний',
        'heavy' => 'Сильный',
        'desaturate' => 'Обесцветить',
        'vivid' => 'Яркий',
        'normal' => 'Нормально',
        'cool' => 'Холодный',
        'warm' => 'Тёплый',
        'soft' => 'Мягкий',
        'sharp' => 'Резкий',
      ),
    ),
  ),
);
