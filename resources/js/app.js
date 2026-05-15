import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

const birI18n = window.__BIR_I18N__ || {};

function t(path, fallback) {
    const value = path.split('.').reduce((acc, part) => (acc && acc[part] !== undefined ? acc[part] : undefined), birI18n);

    return value ?? fallback;
}

// ── Image Processor (upload form) ──────────────────────────────────────────
Alpine.data('imageProcessor', () => ({
    files: [],
    dragging: false,
    submitting: false,
    errors: [],
    fieldErrors: {},
    debug: false,
    isMobileLayout: window.innerWidth < 1024,
    mobilePanels: {
        format: true,
        resize: false,
        rotate: false,
        flip: false,
        rename: false,
    },

    openPicker() {
        this.clearErrors();
        this.$refs.fileInput?.click();
    },

    openPickerFromZone(e) {
        if (e.target.closest('button, a, input')) {
            return;
        }

        this.openPicker();
    },

    onFileChange(e) {
        this.clearErrors();
        this.addFiles(Array.from(e.target.files));
        e.target.value = '';
    },

    onDrop(e) {
        this.dragging = false;
        this.clearErrors();
        this.addFiles(Array.from(e.dataTransfer.files));
    },

    addFiles(newFiles) {
        const knownFiles = new Set(this.files.map(file => this.fileSignature(file.raw)));

        newFiles.forEach(f => {
            if (!f.type.startsWith('image/')) return;
            if (knownFiles.has(this.fileSignature(f))) return;

            knownFiles.add(this.fileSignature(f));

            this.files.push({
                name: f.name,
                preview: URL.createObjectURL(f),
                raw: f,
            });
        });
    },

    fileSignature(file) {
        return [file.name, file.size, file.lastModified].join(':');
    },

    formatFileSize(size) {
        if (size < 1024) {
            return `${size} B`;
        }

        const units = ['KB', 'MB', 'GB'];
        let value = size / 1024;
        let index = 0;

        while (value >= 1024 && index < units.length - 1) {
            value /= 1024;
            index++;
        }

        return `${value.toFixed(1)} ${units[index]}`;
    },

    removeFile(idx) {
        const [removed] = this.files.splice(idx, 1);
        if (removed?.preview) {
            URL.revokeObjectURL(removed.preview);
        }

        if (this.files.length > 0) {
            delete this.fieldErrors.files;
        }
    },

    clearFiles() {
        this.files.forEach(file => {
            if (file.preview) {
                URL.revokeObjectURL(file.preview);
            }
        });

        this.files = [];
        this.$refs.fileInput.value = '';
    },

    toggleMobilePanel(panel) {
        if (!this.isMobileLayout || !Object.hasOwn(this.mobilePanels, panel)) {
            return;
        }

        this.mobilePanels[panel] = !this.mobilePanels[panel];
    },

    panelVisible(panel) {
        if (!this.isMobileLayout) {
            return true;
        }

        return !!this.mobilePanels[panel];
    },

    syncViewport() {
        this.isMobileLayout = window.innerWidth < 1024;
    },

    clearErrors() {
        this.errors = [];
        this.fieldErrors = {};
    },

    logDebug(message, payload = null) {
        if (!this.debug) return;
        console.debug('[imageProcessor]', message, payload);
    },

    buildFormData(form) {
        const formData = new FormData(form);
        formData.delete('files[]');
        formData.delete('files');

        this.files.forEach(file => {
            formData.append('files[]', file.raw, file.raw.name);
        });

        return formData;
    },

    async submitForm(e) {
        e?.preventDefault?.();

        if (this.submitting) {
            return;
        }

        this.clearErrors();

        if (this.files.length === 0) {
            this.errors = [t('upload.add_file', 'Добавьте хотя бы один файл.')];
            this.fieldErrors.files = t('upload.add_file', 'Добавьте хотя бы один файл.');
            return;
        }

        this.submitting = true;

        const form = e?.target instanceof HTMLFormElement
            ? e.target
            : document.getElementById('job-form');

        if (!form) {
            this.errors = [t('upload.form_not_found', 'Не удалось найти форму отправки.')];
            this.submitting = false;
            return;
        }

        try {
            this.logDebug('submit:start', {
                action: form.action,
                files: this.files.map(file => ({ name: file.name, size: file.raw.size })),
            });

            const response = await window.axios.post(form.action, this.buildFormData(form), {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json',
                },
                maxBodyLength: Infinity,
            });

            const redirectUrl = response?.data?.redirect_url || response?.request?.responseURL;

            this.logDebug('submit:success', {
                status: response?.status,
                redirectUrl,
            });

            if (redirectUrl) {
                window.location.assign(redirectUrl);
                return;
            }

            this.errors = [t('upload.redirect_missing', 'Форма отправлена, но адрес результата не получен.')];
        } catch (error) {
            this.logDebug('submit:error', {
                status: error.response?.status,
                data: error.response?.data,
                message: error.message,
            });

            if (error.response?.status === 422) {
                const validationErrors = error.response.data?.errors ?? {};

                this.fieldErrors = Object.fromEntries(
                    Object.entries(validationErrors).map(([field, messages]) => [field, messages[0]])
                );
                this.errors = Object.values(validationErrors).flat();
            } else {
                this.errors = [error.response?.data?.message || t('upload.submit_error', 'Ошибка отправки формы. Попробуйте ещё раз.')];
            }
        } finally {
            this.submitting = false;
        }
    },

    init() {
        const form = document.getElementById('job-form');
        this.debug = window.location.search.includes('debugUpload=1');
        this.syncViewport();
        window.addEventListener('resize', () => this.syncViewport());

        form?.addEventListener('reset', () => {
            this.clearFiles();
            this.clearErrors();
        });
    }
}));

// ── Job Poller (status page) ────────────────────────────────────────────────
Alpine.data('jobPoller', (uuid, initialStatus) => ({
    uuid,
    status: initialStatus,
    progress: 0,
    processed: 0,
    failed: 0,
    total: 0,
    isFinished: false,
    downloadUrl: null,
    pollInterval: null,
    resultFiles: [],
    previewOpen: false,
    previewFile: null,
    previewIndex: -1,

    get statusLabel() {
        const map = {
            pending:    t('job.status.pending', 'В очереди'),
            processing: t('job.status.processing', 'Обработка'),
            done:       t('job.status.done', 'Готово'),
            failed:     t('job.status.failed', 'Ошибка'),
            expired:    t('job.status.expired', 'Истёк срок'),
        };
        return map[this.status] || this.status;
    },

    get statusClass() {
        const map = {
            pending:    'bg-yellow-900/50 text-yellow-400',
            processing: 'bg-blue-900/50 text-blue-400',
            done:       'bg-emerald-900/50 text-emerald-400',
            failed:     'bg-red-900/50 text-red-400',
            expired:    'bg-gray-800 text-gray-500',
        };
        return map[this.status] || 'bg-gray-800 text-gray-400';
    },

    get statusBarClass() {
        if (this.status === 'done')   return 'bg-emerald-500';
        if (this.status === 'failed') return 'bg-red-500';
        return 'bg-violet-500';
    },

    init() {
        if (this.status === 'done') {
            this.loadResultFiles();
        }

        if (!['done', 'failed', 'expired'].includes(this.status)) {
            this.poll();
            this.pollInterval = setInterval(() => this.poll(), 2500);
        }
    },

    async poll() {
        try {
            const res  = await fetch(`/jobs/${this.uuid}/status`);
            const data = await res.json();
            this.status     = data.status;
            this.progress   = data.progress;
            this.processed  = data.processed_files;
            this.failed     = data.failed_files;
            this.total      = data.total_files;
            this.isFinished = data.is_finished;
            this.downloadUrl= data.download_url;
            if (data.is_finished) {
                clearInterval(this.pollInterval);
                if (data.status === 'done') {
                    this.loadResultFiles();
                }
            }
        } catch (e) {
            console.warn('Poll error', e);
        }
    },

    async loadResultFiles() {
        try {
            const response = await fetch(`/jobs/${this.uuid}/result-files`);
            if (!response.ok) {
                return;
            }

            const data = await response.json();
            this.resultFiles = Array.isArray(data.files) ? data.files : [];
        } catch (e) {
            console.warn('Result files load error', e);
        }
    },

    openPreview(file) {
        this.previewIndex = this.resultFiles.findIndex(item => item.id === file.id);
        this.previewFile = file;
        this.previewOpen = true;
        document.body.style.overflow = 'hidden';
    },

    nextPreview() {
        if (this.resultFiles.length < 2) {
            return;
        }

        const next = (this.previewIndex + 1) % this.resultFiles.length;
        this.previewIndex = next;
        this.previewFile = this.resultFiles[next];
    },

    prevPreview() {
        if (this.resultFiles.length < 2) {
            return;
        }

        const prev = (this.previewIndex - 1 + this.resultFiles.length) % this.resultFiles.length;
        this.previewIndex = prev;
        this.previewFile = this.resultFiles[prev];
    },

    onKeyDown(event) {
        if (!this.previewOpen) {
            return;
        }

        if (event.key === 'Escape') {
            this.closePreview();
            return;
        }

        if (event.key === 'ArrowRight') {
            this.nextPreview();
            return;
        }

        if (event.key === 'ArrowLeft') {
            this.prevPreview();
        }
    },

    fileMeta(file) {
        const dimensions = file.width && file.height ? `${file.width}x${file.height}px` : null;
        const size = this.formatFileSize(file.size ?? 0);

        return dimensions ? `${dimensions} · ${size}` : size;
    },

    closePreview() {
        this.previewOpen = false;
        this.previewFile = null;
        this.previewIndex = -1;
        document.body.style.overflow = '';
    },

    formatFileSize(bytes) {
        if (!bytes || bytes < 1024) {
            return `${bytes || 0} B`;
        }

        const units = ['KB', 'MB', 'GB'];
        let value = bytes / 1024;
        let i = 0;

        while (value >= 1024 && i < units.length - 1) {
            value /= 1024;
            i++;
        }

        return `${value.toFixed(1)} ${units[i]}`;
    },
}));

Alpine.data('adminJobsTable', () => ({
    selected: [],

    get allChecked() {
        const checkboxes = this.pageCheckboxes();

        return checkboxes.length > 0 && checkboxes.every(checkbox => this.selected.includes(checkbox.value));
    },

    toggleAll(event) {
        const checked = event.target.checked;
        const values = this.pageCheckboxes().map(checkbox => checkbox.value);

        if (checked) {
            this.selected = Array.from(new Set([...this.selected, ...values]));
            return;
        }

        this.selected = this.selected.filter(value => !values.includes(value));
    },

    pageCheckboxes() {
        return Array.from(this.$root.querySelectorAll('input[name="job_ids[]"]'));
    },
}));

Alpine.start();
