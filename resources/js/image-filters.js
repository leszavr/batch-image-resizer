/**
 * Image Processing Platform - Image Filters Component
 * Real-time filter preview using Canvas API
 */

class ImageFilters {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error(`Canvas with id "${canvasId}" not found`);
            return;
        }
        
        this.ctx = this.canvas.getContext('2d');
        this.originalImage = null;
        this.currentImage = null;
        
        // Default filter values
        this.filters = {
            brightness: 0,    // -100 to 100
            contrast: 0,      // -100 to 100
            saturation: 100,  // 0 to 200
            blur: 0,          // 0 to 20
            sepia: 0,         // 0 to 100
            grayscale: 0,     // 0 to 100
            hueRotate: 0,     // 0 to 360
        };
        
        // Presets
        this.presets = {
            normal: { brightness: 0, contrast: 0, saturation: 100, blur: 0, sepia: 0, grayscale: 0, hueRotate: 0 },
            grayscale: { brightness: 0, contrast: 0, saturation: 0, blur: 0, sepia: 0, grayscale: 100, hueRotate: 0 },
            sepia: { brightness: 0, contrast: 0, saturation: 100, blur: 0, sepia: 100, grayscale: 0, hueRotate: 0 },
            vintage: { brightness: 10, contrast: 20, saturation: 80, blur: 0, sepia: 50, grayscale: 0, hueRotate: 0 },
            cool: { brightness: 0, contrast: 10, saturation: 90, blur: 0, sepia: 0, grayscale: 0, hueRotate: 180 },
            warm: { brightness: 10, contrast: 10, saturation: 110, blur: 0, sepia: 30, grayscale: 0, hueRotate: 30 },
            dramatic: { brightness: -10, contrast: 50, saturation: 120, blur: 0, sepia: 0, grayscale: 0, hueRotate: 0 },
        };
        
        this.options = {
            maxWidth: 800,
            maxHeight: 600,
            quality: 0.9,
            ...options
        };
        
        this.onChange = options.onChange || (() => {});
    }

    /**
     * Load image from File or URL
     */
    loadImage(source) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            
            img.onload = () => {
                this.originalImage = img;
                this.currentImage = img;
                this.fitCanvasToImage();
                this.applyFilters();
                resolve(img);
            };
            
            img.onerror = () => reject(new Error('Failed to load image'));
            
            if (source instanceof File) {
                const reader = new FileReader();
                reader.onload = (e) => img.src = e.target.result;
                reader.readAsDataURL(source);
            } else {
                img.src = source;
            }
        });
    }

    /**
     * Fit canvas to image while maintaining aspect ratio
     */
    fitCanvasToImage() {
        if (!this.originalImage) return;
        
        const { maxWidth, maxHeight } = this.options;
        let { width, height } = this.originalImage;
        
        // Calculate scale to fit within max dimensions
        const scale = Math.min(
            maxWidth / width,
            maxHeight / height,
            1
        );
        
        this.canvas.width = Math.floor(width * scale);
        this.canvas.height = Math.floor(height * scale);
    }

    /**
     * Build CSS filter string from current filter values
     */
    buildFilterString() {
        const f = this.filters;
        return [
            `brightness(${100 + f.brightness}%)`,
            `contrast(${100 + f.contrast}%)`,
            `saturate(${f.saturation}%)`,
            `blur(${f.blur}px)`,
            `sepia(${f.sepia}%)`,
            `grayscale(${f.grayscale}%)`,
            `hue-rotate(${f.hueRotate}deg)`,
        ].join(' ');
    }

    /**
     * Apply filters and redraw canvas
     */
    applyFilters() {
        if (!this.originalImage) return;
        
        // Clear canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Apply filters
        this.ctx.filter = this.buildFilterString();
        
        // Draw image
        this.ctx.drawImage(this.originalImage, 0, 0, this.canvas.width, this.canvas.height);
        
        // Reset filter for future draw operations
        this.ctx.filter = 'none';
        
        // Trigger change callback
        this.onChange(this.getFilterData());
    }

    /**
     * Update a single filter value
     */
    setFilter(name, value) {
        if (this.filters.hasOwnProperty(name)) {
            this.filters[name] = parseFloat(value);
            this.applyFilters();
        }
    }

    /**
     * Get current filter values
     */
    getFilters() {
        return { ...this.filters };
    }

    /**
     * Set multiple filters at once
     */
    setFilters(filters) {
        Object.assign(this.filters, filters);
        this.applyFilters();
    }

    /**
     * Reset all filters to defaults
     */
    reset() {
        this.filters = {
            brightness: 0,
            contrast: 0,
            saturation: 100,
            blur: 0,
            sepia: 0,
            grayscale: 0,
            hueRotate: 0,
        };
        this.applyFilters();
    }

    /**
     * Apply a preset
     */
    applyPreset(presetName) {
        if (this.presets[presetName]) {
            this.setFilters(this.presets[presetName]);
        }
    }

    /**
     * Get available presets
     */
    getPresets() {
        return Object.keys(this.presets);
    }

    /**
     * Get filter data for form submission
     */
    getFilterData() {
        return {
            enabled: this.hasActiveFilters(),
            filters: this.filters,
            cssFilter: this.buildFilterString(),
        };
    }

    /**
     * Check if any filter is active (non-default)
     */
    hasActiveFilters() {
        const defaults = { brightness: 0, contrast: 0, saturation: 100, blur: 0, sepia: 0, grayscale: 0, hueRotate: 0 };
        return Object.keys(this.filters).some(key => this.filters[key] !== defaults[key]);
    }

    /**
     * Export current canvas as data URL
     */
    exportDataURL(type = 'image/jpeg', quality = 0.9) {
        return this.canvas.toDataURL(type, quality);
    }

    /**
     * Export current canvas as Blob
     */
    exportBlob(type = 'image/jpeg', quality = 0.9) {
        return new Promise((resolve) => {
            this.canvas.toBlob((blob) => resolve(blob), type, quality);
        });
    }

    /**
     * Create UI controls
     */
    static createControls(containerId, onChange) {
        const container = document.getElementById(containerId);
        if (!container) return null;

        const controls = document.createElement('div');
        controls.className = 'filter-controls';
        
        const sliders = [
            { name: 'brightness', label: 'Brightness', min: -100, max: 100, default: 0, unit: '%' },
            { name: 'contrast', label: 'Contrast', min: -100, max: 100, default: 0, unit: '%' },
            { name: 'saturation', label: 'Saturation', min: 0, max: 200, default: 100, unit: '%' },
            { name: 'blur', label: 'Blur', min: 0, max: 20, default: 0, unit: 'px' },
            { name: 'sepia', label: 'Sepia', min: 0, max: 100, default: 0, unit: '%' },
            { name: 'grayscale', label: 'Grayscale', min: 0, max: 100, default: 0, unit: '%' },
            { name: 'hueRotate', label: 'Hue Rotate', min: 0, max: 360, default: 0, unit: '°' },
        ];

        const inputs = {};

        sliders.forEach(slider => {
            const wrapper = document.createElement('div');
            wrapper.className = 'filter-control';
            
            const header = document.createElement('div');
            header.className = 'filter-control-header';
            
            const label = document.createElement('label');
            label.textContent = slider.label;
            
            const value = document.createElement('span');
            value.className = 'filter-value';
            value.textContent = slider.default + slider.unit;
            
            header.appendChild(label);
            header.appendChild(value);
            
            const input = document.createElement('input');
            input.type = 'range';
            input.name = `filter_${slider.name}`;
            input.min = slider.min;
            input.max = slider.max;
            input.value = slider.default;
            input.className = 'filter-slider';
            
            input.addEventListener('input', (e) => {
                const val = parseInt(e.target.value);
                value.textContent = val + slider.unit;
                if (onChange) onChange(slider.name, val);
            });
            
            inputs[slider.name] = input;
            wrapper.appendChild(header);
            wrapper.appendChild(input);
            controls.appendChild(wrapper);
        });

        // Presets
        const presetsWrapper = document.createElement('div');
        presetsWrapper.className = 'filter-presets';
        
        const presetsLabel = document.createElement('label');
        presetsLabel.textContent = 'Presets:';
        presetsWrapper.appendChild(presetsLabel);
        
        const presetButtons = document.createElement('div');
        presetButtons.className = 'preset-buttons';
        
        ['normal', 'grayscale', 'sepia', 'vintage', 'cool', 'warm', 'dramatic'].forEach(preset => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'preset-btn';
            btn.textContent = preset.charAt(0).toUpperCase() + preset.slice(1);
            btn.dataset.preset = preset;
            presetButtons.appendChild(btn);
        });
        
        presetsWrapper.appendChild(presetButtons);
        controls.appendChild(presetsWrapper);

        // Reset button
        const resetBtn = document.createElement('button');
        resetBtn.type = 'button';
        resetBtn.className = 'filter-reset-btn';
        resetBtn.textContent = 'Reset All';
        resetBtn.addEventListener('click', () => {
            sliders.forEach(s => {
                inputs[s.name].value = s.default;
                inputs[s.name].dispatchEvent(new Event('input'));
            });
        });
        controls.appendChild(resetBtn);

        container.appendChild(controls);
        
        return { container: controls, inputs, presetButtons };
    }
}

// Alpine.js integration
function filtersComponent() {
    return {
        filters: null,
        previewUrl: null,
        canvas: null,
        
        init() {
            this.canvas = new ImageFilters('preview-canvas', {
                onChange: (data) => {
                    this.$dispatch('filters-changed', data);
                }
            });
        },
        
        loadPreview(file) {
            if (file) {
                this.canvas.loadImage(file).then(() => {
                    this.previewUrl = this.canvas.exportDataURL();
                });
            }
        },
        
        updateFilter(name, value) {
            this.canvas.setFilter(name, value);
        },
        
        applyPreset(name) {
            this.canvas.applyPreset(name);
        },
        
        resetFilters() {
            this.canvas.reset();
        },
        
        getFilterData() {
            return this.canvas.getFilterData();
        }
    };
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ImageFilters, filtersComponent };
}

// Make globally available
window.ImageFilters = ImageFilters;
window.filtersComponent = filtersComponent;