<?php

return array (
  '_meta' => 
  array (
    'code' => 'en',
    'name' => 'English',
    'native_name' => 'English',
    'is_active' => true,
    'is_default' => true,
  ),
  'translations' => 
  array (
    'brand' => 'IPP',
    'meta' => 
    array (
      'description' => 'Free online tool for batch image resizing, conversion, and processing',
    ),
    'nav' => 
    array (
      'tools' => 'Tools',
      'plans' => 'Pricing',
      'dashboard' => 'Dashboard',
      'login' => 'Sign in',
      'register' => 'Sign up',
      'logout' => 'Sign out',
      'open_menu' => 'Open menu',
    ),
    'lang' => 
    array (
      'label' => 'Language',
    ),
    'footer' => 
    array (
      'copyright' => 'Online image processing',
      'ttl' => 'Files are stored for :hours hours and deleted automatically',
    ),
    'common' => 
    array (
      'save' => 'Save',
      'delete' => 'Delete',
      'actions' => 'Actions',
      'reset' => 'Reset',
      'filter' => 'Filter',
      'search' => 'Search',
      'all' => 'All',
      'yes' => 'Yes',
      'no' => 'No',
      'guest' => 'guest',
      'clear_all' => 'Clear all',
      'cancel' => 'Cancel',
      'back' => 'Back',
    ),
    'roles' => 
    array (
      'user' => 'User',
      'admin' => 'Administrator',
      'superadmin' => 'Superadmin',
    ),
    'status' => 
    array (
      'job' => 
      array (
        'pending' => 'Queued',
        'processing' => 'Processing',
        'done' => 'Done',
        'failed' => 'Error',
        'expired' => 'Expired',
      ),
    ),
    'auth' => 
    array (
      'login_title' => 'Sign in',
      'register_title' => 'Sign up',
      'email' => 'Email',
      'password' => 'Password',
      'password_confirm' => 'Confirm password',
      'remember' => 'Remember me',
      'login_btn' => 'Sign in',
      'register_btn' => 'Create account',
      'wrong_credentials' => 'Invalid email or password.',
      'welcome' => 'Welcome!',
      'name' => 'Name',
      'name_placeholder' => 'Your name',
    ),
    'dashboard' => 
    array (
      'admin_access' => 
      array (
        'title' => 'Administrative access',
        'description' => 'You have access to the admin panel to manage users, jobs, and pricing.',
        'action' => 'Open admin panel',
      ),
      'current_plan' => 'Current plan',
      'jobs_today' => 'Jobs today',
      'credits' => 'Credits',
      'recent_jobs' => 'Recent jobs',
      'job_fallback_name' => 'Job #:uuid',
      'no_jobs' => 'No jobs yet.',
    ),
    'history' => 
    array (
      'title' => 'Job history',
      'status' => 'Status',
      'files' => 'Files',
      'date' => 'Date',
      'actions' => 'Actions',
      'open' => 'Open',
      'empty' => 'No jobs yet',
    ),
    'job' => 
    array (
      'title' => 'Job #:uuid',
      'new_job' => 'New job',
      'processing_title' => 'Image processing',
      'files_label' => 'Files',
      'processed' => 'Processed',
      'errors' => 'Errors',
      'pending_message' => 'Job is in queue, please wait...',
      'download_zip' => 'Download ZIP',
      'archive_ttl' => 'Archive will be available for :hours hours',
      'processed_files' => 'Processed files',
      'failed_message' => 'Processing finished with errors. Check the file list below.',
      'files_section' => 'Files',
    ),
    'presets' => 
    array (
      'title' => 'Presets',
      'name' => 'Name',
      'save' => 'Save',
      'delete' => 'Delete',
      'empty' => 'No presets yet.',
      'messages' => 
      array (
        'pipeline_json' => 'Pipeline must be a valid JSON array.',
        'pipeline_array' => 'Pipeline must be an array of steps.',
        'saved' => 'Preset saved.',
        'deleted' => 'Preset deleted.',
      ),
    ),
    'plans' => 
    array (
      'popular' => 'Popular',
      'max_files' => 'Files per job',
      'max_file_size' => 'Max file size',
      'daily_limit' => 'Daily limit',
      'ai_credits' => 'AI credits / month',
      'price' => 
      array (
        'free' => 'Free',
        'month_short' => 'mo',
      ),
    ),
    'jobs' => 
    array (
        'messages' => 
        array (
          'created' => 'Job created and queued.',
          'daily_limit_reached' => 'You have reached your daily job limit (:limit/day). Upgrade your plan.',
          'max_files_exceeded' => 'Maximum :max files per job on your current plan.',
          'file_too_large' => 'File exceeds the :limit MB limit.',
          'output_format_unavailable' => 'Selected format is not available on your plan.',
          'watermark_unavailable' => 'Watermark feature is not available on your plan.',
          'operation_unavailable' => 'Operation :operation is not available on your plan.',
          'archive_not_ready' => 'Archive is not ready yet.',
          'archive_missing' => 'Archive not found or storage period expired.',
          'filter_validation' => 
          array (
            'brightness' => 'Brightness must be between -100 and 100.',
            'contrast' => 'Contrast must be between -100 and 100.',
            'saturation' => 'Saturation must be between 0 and 200.',
            'blur' => 'Blur must be between 0 and 20.',
            'sepia' => 'Sepia must be between 0 and 100.',
            'grayscale' => 'Grayscale must be between 0 and 100.',
            'hue_rotate' => 'Hue rotate must be between 0 and 360.',
          ),
        ),
    ),
    'api' => 
    array (
      'access_denied' => 'API access is not available on your current plan.',
    ),
    'admin' => 
    array (
      'common' => 
      array (
        'title' => 'Admin',
      ),
      'nav' => 
      array (
        'overview' => 'Overview',
        'users' => 'Users',
        'jobs' => 'Jobs',
        'plans' => 'Plans',
        'localization' => 'Localization',
        'statistics' => 'Statistics',
      ),
      'dashboard' => 
      array (
        'subtitle' => 'Basic platform overview and quick actions.',
        'stats' => 
        array (
          'users' => 'Users',
          'plans' => 'Plans',
          'jobs_today' => 'Jobs today',
          'jobs_processing' => 'Queued / processing',
          'expired_archives' => 'Expired archives',
          'stale_jobs' => 'Potentially stale jobs',
        ),
        'quick_actions' => 
        array (
          'title' => 'Quick actions',
          'cleanup' => 'Clean expired archives',
          'stop_stale' => 'Stop stale jobs',
          'stale_threshold' => 'Stale job threshold: older than :time.',
        ),
        'available_now' => 
        array (
          'title' => 'Available now',
          'cleanup' => 'Cleanup of unclaimed archives and result files.',
          'stop_stale' => 'Manual stop of stale pending/processing jobs.',
          'plan_editing' => 'Plan editing without overengineered constructors.',
        ),
        'recent_jobs' => 
        array (
          'title' => 'Recent jobs',
          'user' => 'User',
          'status' => 'Status',
          'files' => 'Files',
          'created' => 'Created',
          'empty' => 'No jobs yet.',
        ),
      ),
      'users' => 
      array (
        'title' => 'Admin — users',
        'subtitle' => 'Basic user management: roles, plans, and credits.',
        'search_placeholder' => 'Search by name or email',
        'registered' => 'Registered',
        'plan' => 'Plan',
        'no_plan' => 'No plan',
        'roles' => 'Roles',
        'effective_plan' => 'Effective plan',
        'unlimited_access' => 'Unlimited access',
        'empty' => 'No users found.',
        'messages' => 
        array (
          'created' => 'User :email created.',
          'updated' => 'User :email updated.',
          'deleted' => 'User :email deleted.',
          'password_reset' => 'Password for :email changed.',
          'cannot_delete_self' => 'Cannot delete yourself.',
        ),
        'create_title' => 'Add User',
        'create_subtitle' => 'Create new user manually',
        'password_min' => 'Minimum 8 characters',
        'new_password' => 'New password',
        'confirm_delete' => 'Delete user :email? All their data will be permanently removed.',
        'total_jobs' => 'Total jobs',
        'blocking' => 
        array (
          'title' => 'Blocking',
          'permanent' => 'Permanent block',
          'until' => 'Blocked until',
          'reason' => 'Reason',
          'reason_placeholder' => 'Enter block reason',
        ),
        'status' => 
        array (
          'blocked_permanent' => 'Blocked',
          'blocked_until' => 'Blocked until :until',
        ),
        'fields' => 
        array (
          'unlimited_access' => 'Unlimited access',
        ),
        'actions' => 
        array (
          'create' => 'Add user',
          'reset_password' => 'Reset password',
          'reset_password_confirm' => 'Change password',
          'stats' => 'Statistics',
        ),
        'stats_title' => 'User Statistics',
        'stats' => 
        array (
          'total_jobs' => 'Total jobs',
          'jobs_today' => 'Today',
          'jobs_this_week' => 'This week',
          'jobs_this_month' => 'This month',
          'activity_chart' => 'Activity chart',
          'recent_jobs' => 'Recent jobs',
          'no_jobs' => 'No jobs',
          'jobs_per_day' => 'Jobs per day',
        ),
      ),
      'jobs' => 
      array (
        'title' => 'Admin — jobs',
        'subtitle' => 'Filtering, sorting, single and bulk deletion, expired archive cleanup, and stale job stopping.',
        'filters' => 
        array (
          'status' => 'Status',
          'expired_only' => 'Expired only',
          'stale_only' => 'Stale only',
          'sort' => 'Sort by',
          'sort_created' => 'Created at',
          'sort_expires' => 'Expires at',
          'sort_status' => 'Status',
          'direction' => 'Direction',
          'direction_desc' => 'Newest first',
          'direction_asc' => 'Oldest first',
        ),
        'actions' => 
        array (
          'cleanup' => 'Clean expired archives',
          'stop_stale' => 'Stop stale jobs',
        ),
        'stale_hint' => 'Stale = pending/processing older than :time.',
        'bulk' => 
        array (
          'select_all' => 'Select all on this page',
          'selected' => 'Selected',
          'delete' => 'Delete selected',
        ),
        'table' => 
        array (
          'user' => 'User',
          'status' => 'Status',
          'archive' => 'Archive',
          'expires' => 'Expires',
          'created' => 'Created',
          'actions' => 'Actions',
        ),
        'confirm' => 
        array (
          'bulk_delete' => 'Delete selected jobs? This will remove archives, sources, and results.',
          'delete_single' => 'Delete this job? Archive, sources, and results will be removed.',
        ),
        'empty' => 'Nothing found.',
        'messages' => 
        array (
          'cleaned' => 'Expired archives/results cleaned: :count.',
          'marked_stale_file' => 'Job was marked stale by an administrator.',
          'stopped_stale' => 'Marked as stale and stopped: :count jobs.',
          'deleted' => 'Job :uuid deleted.',
          'bulk_deleted' => 'Deleted jobs: :count.',
        ),
      ),
      'plans' => 
      array (
        'title' => 'Admin — plans',
        'subtitle' => 'Manage plans and basic usage analytics.',
        'create_title' => 'Add plan',
        'translation_locale' => 'Translation locale',
        'translation_hint' => 'Edit plan translations here for the selected locale.',
        'fields' => 
        array (
          'name' => 'Name',
          'description' => 'Description',
          'price_month' => 'Price / month',
          'price_year' => 'Price / year',
          'currency' => 'Currency',
          'max_files' => 'Files per job',
          'max_file_size' => 'File size, MB',
          'daily_limit' => 'Daily limit',
          'monthly_credits' => 'Credits / month',
          'sort_order' => 'Sort order',
          'allowed_formats' => 'Allowed formats',
          'allowed_operations' => 'Allowed operations',
          'feature_flags' => 'Plan features',
          'storage_ttl_hours' => 'Storage TTL, hours',
        ),
        'actions' => 
        array (
          'create' => 'Add plan',
        ),
        'analytics' => 
        array (
          'users' => 'users',
          'subscriptions' => 'Subscriptions',
          'jobs' => 'Jobs',
        ),
        'features' => 
        array (
          'watermark' => 'Watermark',
          'api_access' => 'API access',
          'priority_queue' => 'Priority queue',
          'ai_features' => 'AI features',
          'is_active' => 'Active',
          'is_popular' => 'Popular',
        ),
        'confirm_delete' => 'Delete plan :name?',
        'messages' => 
        array (
          'created' => 'Plan created.',
          'updated' => 'Plan :name updated.',
          'deleted' => 'Plan :name deleted.',
          'delete_blocked' => 'Cannot delete plan: users or subscriptions are linked to it.',
        ),
      ),
      'localization' => 
      array (
        'title' => 'Admin — localization',
        'subtitle' => 'Manage interface languages, UI translations, and localized plan content.',
        'add_locale' => 'Add language',
        'existing_locales' => 'Existing languages',
        'ui_translations' => 'UI translations',
        'search_placeholder' => 'Filter by key or text',
        'source_locale' => 'Source locale',
        'editing_locale' => 'Editing locale',
        'saving' => 'Saving...',
        'saved' => 'Saved',
        'table_hint' => 'Rows in table: :count',
        'source_text' => 'Source text',
        'translation_text' => 'Translation',
        'state' => 'Source',
        'add_single_translation' => 'Add a single translation manually',
        'optional_helper' => 'optional',
        'code' => 'Code',
        'name' => 'Name',
        'native_name' => 'Native name',
        'is_active' => 'Active',
        'is_default' => 'Default',
        'group' => 'Group',
        'key' => 'Key',
        'locale' => 'Locale',
        'value' => 'Value',
        'empty_translations' => 'No UI translations in DB yet.',
        'confirm_delete_locale' => 'Delete locale :code? DB translations for this locale will be removed.',
        'actions' => 
        array (
          'add_locale' => 'Add language',
          'make_default' => 'Set as default',
          'save_locale' => 'Save language',
          'save_translation' => 'Save UI translation',
          'save_translations' => 'Save translations',
        ),
        'messages' => 
        array (
          'locale_created' => 'Locale created.',
          'locale_updated' => 'Locale updated.',
          'locale_deleted' => 'Locale deleted.',
          'locale_delete_default_blocked' => 'Default locale cannot be deleted.',
          'translation_saved' => 'UI translation saved.',
          'translations_updated' => 'Translations updated.',
        ),
      ),
      'statistics' => 
      array (
        'title' => 'Statistics',
        'subtitle' => 'Platform overview statistics',
        'period' => 
        array (
          'week' => 'Week',
          'month' => 'Month',
          'quarter' => 'Quarter',
        ),
        'today' => 'today',
        'total_users' => 'Total users',
        'total_jobs' => 'Total jobs',
        'active_subscriptions' => 'Active subscriptions',
        'total_revenue' => 'Total revenue',
        'users_growth' => 'User growth',
        'jobs_activity' => 'Job activity',
        'revenue' => 'Revenue',
        'top_users' => 'Top users',
        'jobs_count' => 'Jobs',
        'new_users' => 'New users',
        'jobs_per_day' => 'Jobs per day',
        'daily_revenue' => 'Daily revenue',
        'no_data' => 'No data',
      ),
    ),
    'app' => 
    array (
      'title' => 'Batch image processing online',
      'hero' => 
      array (
        'title' => 'Batch image processing',
        'description' => 'Upload images, change format, size, and quality. Process dozens of files in one run and download the final ZIP.',
      ),
      'steps' => 
      array (
        'upload' => 'Upload files',
        'configure' => 'Configure tools',
        'download' => 'Download result',
      ),
      'submit_failed' => 'Failed to submit the form',
      'upload_zone_aria' => 'Image upload area',
      'upload_zone_title' => 'Upload images',
      'upload_zone_hint' => 'Drag files here or <span class="text-violet-400 underline">choose</span>',
      'upload_zone_formats' => 'JPG, PNG, WebP, GIF, BMP, TIFF — up to :size MB each',
      'more_files' => 
      array (
        'title' => 'You can add files from another folder',
        'description' => 'Click the upload area or drag new files',
      ),
      'uploaded_files' => 'Uploaded files',
      'sections' => 
      array (
        'format_quality' => 'Format and quality',
        'resize' => 'Resize',
        'rotate' => 'Rotate',
        'flip' => 'Flip',
        'rename' => 'Rename',
        'filters' => 'Image Filters',
      ),
      'quality' => 'Quality (JPEG/WebP)',
      'resize' => 
      array (
        'none' => '— Do not resize —',
        'width' => 'By width',
        'height' => 'By height',
        'fixed' => 'Exact size (px)',
        'fit' => 'Fit into frame',
        'cover' => 'Cover frame (crop)',
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
        'horizontal' => '⇆ Horizontal',
        'vertical' => '⇅ Vertical',
      ),
      'width_px' => 'Width (px)',
      'height_px' => 'Height (px)',
      'allow_upscale' => 'Allow upscaling',
      'rename' => 
      array (
        'mode' => 'Name mode',
        'original' => 'Keep original name',
        'sequence' => 'Sequence',
        'prefix' => 'Prefix',
        'suffix' => 'Suffix',
        'start_number' => 'Sequence starts from',
        'sequence_hint' => 'For the “Sequence” mode: pic001_img.jpg, pic002_img.jpg, etc.',
      ),
      'process' => 'Process',
      'uploading' => 'Uploading files...',
    ),
    'js' => 
    array (
      'upload' => 
      array (
        'add_file' => 'Add at least one file.',
        'form_not_found' => 'Could not find the submit form.',
        'redirect_missing' => 'The form was submitted, but the result URL was not received.',
        'submit_error' => 'Failed to submit the form. Please try again.',
      ),
      'localization' => 
      array (
        'row_save_error' => 'Failed to save the row.',
      ),
      'job' => 
      array (
        'status' => 
        array (
          'pending' => 'Queued',
          'processing' => 'Processing',
          'done' => 'Done',
          'failed' => 'Error',
          'expired' => 'Expired',
        ),
      ),
    ),
    'tools' => 
    array (
      'meta' => 
      array (
        'title' => 'Online Image Tools',
        'index_title' => 'Image Tools — Free Online Editors',
      ),
      'index' => 
      array (
        'title' => 'Image Tools',
        'subtitle' => 'Free online tools to edit and transform your images instantly',
      ),
      'category' => 
      array (
        'basic' => 'Basic Tools',
        'filters' => 'Filters & Effects',
      ),
      'features' => 
      array (
        'dragdrop' => 
        array (
          'title' => 'Drag & Drop',
          'desc' => 'Simply drag your image to start editing',
        ),
        'preview' => 
        array (
          'title' => 'Live Preview',
          'desc' => 'See changes instantly before downloading',
        ),
        'download' => 
        array (
          'title' => 'Instant Download',
          'desc' => 'Get your processed image in seconds',
        ),
      ),
      'back_to_tools' => 'Back to all tools',
      'upload' => 
      array (
        'title' => 'Upload Image',
        'drag_drop' => 'Drag & drop your image here',
        'or_click' => 'or click to browse',
        'processing' => 'Processing...',
        'complete' => 'Complete!',
      ),
      'preview' => 
      array (
        'title' => 'Preview',
        'empty' => 'Upload an image to see the preview',
        'hold_to_compare' => 'Hold to compare',
        'download' => 'Download',
      ),
      'errors' => 
      array (
        'not_image' => 'Please upload a valid image file',
        'too_large' => 'File size exceeds 10MB limit',
        'upload_failed' => 'Upload failed, please try again',
        'processing_failed' => 'Image processing failed',
      ),
      'crop' => 
      array (
        'name' => 'Crop Image',
        'description' => 'Crop your images to any size or aspect ratio',
        'settings' => 'Crop Settings',
        'width' => 'Width (px)',
        'height' => 'Height (px)',
        'position_x' => 'X Position',
        'position_y' => 'Y Position',
      ),
      'rotate' => 
      array (
        'name' => 'Rotate Image',
        'description' => 'Rotate images by any angle',
        'settings' => 'Rotation Settings',
        'angle' => 'Rotation Angle',
      ),
      'flip' => 
      array (
        'name' => 'Flip Image',
        'description' => 'Flip images horizontally or vertically',
        'settings' => 'Flip Settings',
        'direction' => 'Flip Direction',
        'horizontal' => 'Horizontal',
        'vertical' => 'Vertical',
      ),
      'resize' => 
      array (
        'name' => 'Resize Image',
        'description' => 'Resize images to exact dimensions',
        'settings' => 'Resize Settings',
        'width' => 'Width (px)',
        'height' => 'Height (px)',
        'maintain_aspect' => 'Maintain aspect ratio',
        'presets' => 'Quick presets:',
      ),
      'brightness' => 
      array (
        'name' => 'Brightness',
        'description' => 'Adjust image brightness',
        'settings' => 'Brightness Settings',
        'value' => 'Brightness',
      ),
      'contrast' => 
      array (
        'name' => 'Contrast',
        'description' => 'Adjust image contrast',
        'settings' => 'Contrast Settings',
        'value' => 'Contrast',
      ),
      'saturation' => 
      array (
        'name' => 'Saturation',
        'description' => 'Adjust image saturation',
        'settings' => 'Saturation Settings',
        'value' => 'Saturation',
      ),
      'exposure' => 
      array (
        'name' => 'Exposure',
        'description' => 'Control the exposure of your images',
        'settings' => 'Exposure Settings',
        'value' => 'Exposure',
      ),
      'temperature' => 
      array (
        'name' => 'Temperature',
        'description' => 'Change the color temperature of your images',
        'settings' => 'Temperature Settings',
        'value' => 'Temperature',
        'cool' => 'Cool',
        'warm' => 'Warm',
      ),
      'gamma' => 
      array (
        'name' => 'Gamma',
        'description' => 'Fine-tune the gamma levels in your images',
        'settings' => 'Gamma Settings',
        'value' => 'Gamma',
      ),
      'clarity' => 
      array (
        'name' => 'Clarity',
        'description' => 'Improve the clarity of your images',
        'settings' => 'Clarity Settings',
        'value' => 'Clarity',
        'soft' => 'Soft',
        'sharp' => 'Sharp',
      ),
      'filters' => 
      array (
        'name' => 'Photo Filters',
        'description' => 'Apply professional photo filters and effects',
        'settings' => 'Filter Settings',
        'brightness' => 'Brightness',
        'contrast' => 'Contrast',
        'blur' => 'Blur',
        'grayscale' => 'Grayscale',
        'darken' => 'Darken',
        'lighten' => 'Lighten',
        'low' => 'Low',
        'high' => 'High',
        'desaturate' => 'Desaturate',
        'vivid' => 'Vivid',
        'normal' => 'Normal',
        'cool' => 'Cool',
        'warm' => 'Warm',
        'soft' => 'Soft',
        'sharp' => 'Sharp',
      ),
    ),
  ),
);
