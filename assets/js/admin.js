// Admin Panel JavaScript for JEMBARA RISET DAN MEDIA

document.addEventListener('DOMContentLoaded', function() {
    initAdminPanel();
});

function initAdminPanel() {
    // Initialize all admin components
    initDataTables();
    initFormValidation();
    initFileUploads();
    initColorPickers();
    initTextEditors();
    initTooltips();
    initConfirmDialogs();
    initAutoSave();
    initKeyboardShortcuts();
    
    console.log('Admin panel initialized');
}

// Enhanced data tables
function initDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sortable]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.innerHTML += ' <i class="fas fa-sort text-gray-400 ml-1"></i>';
            
            header.addEventListener('click', function() {
                sortTable(table, this);
            });
        });
        
        // Add search functionality if search input exists
        const searchInput = document.querySelector(`[data-table-search="${table.id}"]`);
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterTable(table, this.value);
            });
        }
    });
}

function sortTable(table, header) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
    const currentSort = header.dataset.sort || 'none';
    
    // Reset all header icons
    table.querySelectorAll('th[data-sortable] i').forEach(icon => {
        icon.className = 'fas fa-sort text-gray-400 ml-1';
    });
    
    let newSort;
    if (currentSort === 'none' || currentSort === 'desc') {
        newSort = 'asc';
        header.querySelector('i').className = 'fas fa-sort-up text-primary ml-1';
    } else {
        newSort = 'desc';
        header.querySelector('i').className = 'fas fa-sort-down text-primary ml-1';
    }
    
    header.dataset.sort = newSort;
    
    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.children[columnIndex].textContent.trim();
        const bValue = b.children[columnIndex].textContent.trim();
        
        // Try to parse as numbers first
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return newSort === 'asc' ? aNum - bNum : bNum - aNum;
        }
        
        // Sort as strings
        return newSort === 'asc' ? 
            aValue.localeCompare(bValue) : 
            bValue.localeCompare(aValue);
    });
    
    // Reorder table rows
    rows.forEach(row => tbody.appendChild(row));
}

function filterTable(table, searchTerm) {
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matches = text.includes(searchTerm.toLowerCase());
        row.style.display = matches ? '' : 'none';
    });
}

// Form validation
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';
    
    // Remove existing error
    clearFieldError(field);
    
    // Required validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'Field ini wajib diisi';
    }
    
    // Email validation
    if (field.type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        message = 'Format email tidak valid';
    }
    
    // URL validation
    if (field.type === 'url' && value && !isValidURL(value)) {
        isValid = false;
        message = 'Format URL tidak valid';
    }
    
    // Minimum length
    if (field.hasAttribute('minlength') && value.length < parseInt(field.getAttribute('minlength'))) {
        isValid = false;
        message = `Minimal ${field.getAttribute('minlength')} karakter`;
    }
    
    // Maximum length
    if (field.hasAttribute('maxlength') && value.length > parseInt(field.getAttribute('maxlength'))) {
        isValid = false;
        message = `Maksimal ${field.getAttribute('maxlength')} karakter`;
    }
    
    if (!isValid) {
        showFieldError(field, message);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// File upload handling
function initFileUploads() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        // Create custom upload interface
        createFileUploadInterface(input);
        
        input.addEventListener('change', function(e) {
            handleFileUpload(this, e);
        });
    });
}

function createFileUploadInterface(input) {
    const wrapper = document.createElement('div');
    wrapper.className = 'file-upload-wrapper border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors';
    
    wrapper.innerHTML = `
        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600 mb-2">Klik untuk memilih file atau drag & drop</p>
        <p class="text-sm text-gray-500">Maksimal ukuran: 10MB</p>
        <div class="upload-progress hidden mt-4">
            <div class="bg-gray-200 rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">Uploading...</p>
        </div>
    `;
    
    input.parentNode.insertBefore(wrapper, input);
    input.style.display = 'none';
    
    wrapper.addEventListener('click', () => input.click());
    
    // Drag and drop
    wrapper.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-primary', 'bg-primary-50');
    });
    
    wrapper.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary-50');
    });
    
    wrapper.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            handleFileUpload(input, { target: { files: files } });
        }
    });
}

function handleFileUpload(input, event) {
    const files = event.target.files;
    const wrapper = input.previousElementSibling;
    const progressBar = wrapper.querySelector('.upload-progress');
    
    if (files.length === 0) return;
    
    // Validate file
    const file = files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    if (file.size > maxSize) {
        showNotification('File terlalu besar. Maksimal 10MB.', 'error');
        return;
    }
    
    // Show progress
    progressBar.classList.remove('hidden');
    
    // Simulate upload progress (replace with actual upload logic)
    simulateUploadProgress(progressBar, () => {
        showNotification('File berhasil diupload!', 'success');
        progressBar.classList.add('hidden');
    });
}

function simulateUploadProgress(progressElement, callback) {
    const progressBar = progressElement.querySelector('.bg-primary');
    let progress = 0;
    
    const interval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            setTimeout(callback, 500);
        }
        
        progressBar.style.width = progress + '%';
    }, 200);
}

// Color picker enhancement
function initColorPickers() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    
    colorInputs.forEach(input => {
        const textInput = input.nextElementSibling;
        if (textInput && textInput.type === 'text') {
            // Sync color picker with text input
            input.addEventListener('change', function() {
                textInput.value = this.value;
                updateColorPreview(this);
            });
            
            textInput.addEventListener('input', function() {
                if (isValidHexColor(this.value)) {
                    input.value = this.value;
                    updateColorPreview(input);
                }
            });
        }
        
        // Add color preview
        addColorPreview(input);
    });
}

function addColorPreview(colorInput) {
    const preview = document.createElement('div');
    preview.className = 'color-preview w-8 h-8 rounded border border-gray-300 ml-2';
    preview.style.backgroundColor = colorInput.value;
    
    colorInput.parentNode.appendChild(preview);
}

function updateColorPreview(colorInput) {
    const preview = colorInput.parentNode.querySelector('.color-preview');
    if (preview) {
        preview.style.backgroundColor = colorInput.value;
    }
}

function isValidHexColor(hex) {
    return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
}

// Text editor enhancements
function initTextEditors() {
    const textareas = document.querySelectorAll('textarea[data-editor]');
    
    textareas.forEach(textarea => {
        addTextEditorTools(textarea);
    });
}

function addTextEditorTools(textarea) {
    const toolbar = document.createElement('div');
    toolbar.className = 'text-editor-toolbar bg-gray-100 border border-gray-300 rounded-t-lg p-2 flex space-x-2';
    
    const tools = [
        { icon: 'fas fa-bold', action: 'bold', title: 'Bold' },
        { icon: 'fas fa-italic', action: 'italic', title: 'Italic' },
        { icon: 'fas fa-list-ul', action: 'list', title: 'Bullet List' },
        { icon: 'fas fa-list-ol', action: 'numbered-list', title: 'Numbered List' },
        { icon: 'fas fa-link', action: 'link', title: 'Insert Link' }
    ];
    
    tools.forEach(tool => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'text-editor-tool px-2 py-1 rounded hover:bg-gray-200 transition-colors';
        button.innerHTML = `<i class="${tool.icon}"></i>`;
        button.title = tool.title;
        
        button.addEventListener('click', () => {
            applyTextFormat(textarea, tool.action);
        });
        
        toolbar.appendChild(button);
    });
    
    textarea.parentNode.insertBefore(toolbar, textarea);
    textarea.classList.add('rounded-t-none');
}

function applyTextFormat(textarea, action) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    let replacement = '';
    
    switch (action) {
        case 'bold':
            replacement = `**${selectedText}**`;
            break;
        case 'italic':
            replacement = `*${selectedText}*`;
            break;
        case 'list':
            replacement = `• ${selectedText}`;
            break;
        case 'numbered-list':
            replacement = `1. ${selectedText}`;
            break;
        case 'link':
            const url = prompt('Enter URL:');
            if (url) {
                replacement = `[${selectedText || 'Link Text'}](${url})`;
            }
            break;
    }
    
    if (replacement) {
        textarea.setRangeText(replacement, start, end);
        textarea.focus();
    }
}

// Tooltip system
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            showTooltip(e.target, e.target.dataset.tooltip);
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function showTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.id = 'tooltip';
    tooltip.className = 'fixed z-50 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg pointer-events-none';
    tooltip.textContent = text;
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
}

function hideTooltip() {
    const tooltip = document.getElementById('tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Confirmation dialogs
function initConfirmDialogs() {
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Apakah Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

// Auto-save functionality
function initAutoSave() {
    const forms = document.querySelectorAll('[data-autosave]');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', debounce(() => {
                autoSaveForm(form);
            }, 2000));
        });
        
        // Load saved data on page load
        loadAutoSavedData(form);
    });
}

function autoSaveForm(form) {
    const formData = new FormData(form);
    const data = {};
    
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    const formId = form.id || form.dataset.autosave;
    localStorage.setItem(`autosave_${formId}`, JSON.stringify(data));
    
    // Show auto-save indicator
    showAutoSaveIndicator();
}

function loadAutoSavedData(form) {
    const formId = form.id || form.dataset.autosave;
    const savedData = localStorage.getItem(`autosave_${formId}`);
    
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            
            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field && !field.value) {
                    field.value = data[key];
                }
            });
        } catch (error) {
            console.error('Error loading auto-saved data:', error);
        }
    }
}

function showAutoSaveIndicator() {
    const indicator = document.getElementById('autosave-indicator') || createAutoSaveIndicator();
    indicator.textContent = 'Tersimpan otomatis';
    indicator.classList.remove('hidden');
    
    setTimeout(() => {
        indicator.classList.add('hidden');
    }, 2000);
}

function createAutoSaveIndicator() {
    const indicator = document.createElement('div');
    indicator.id = 'autosave-indicator';
    indicator.className = 'fixed bottom-4 left-4 bg-green-500 text-white px-3 py-2 rounded shadow-lg text-sm hidden';
    document.body.appendChild(indicator);
    return indicator;
}

// Keyboard shortcuts
function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S: Save form
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const activeForm = document.querySelector('form:focus-within');
            if (activeForm) {
                const submitButton = activeForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.click();
                }
            }
        }
        
        // Ctrl/Cmd + N: New item
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            const newButton = document.querySelector('[data-action="new"], .btn-new');
            if (newButton) {
                newButton.click();
            }
        }
        
        // Escape: Close modals
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal:not(.hidden)');
            if (openModal) {
                const closeButton = openModal.querySelector('.modal-close');
                if (closeButton) {
                    closeButton.click();
                }
            }
        }
    });
}

// Utility functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidURL(url) {
    try {
        new URL(url);
        return true;
    } catch (error) {
        return false;
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform translate-x-full transition-transform duration-300`;
    
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-500', 'text-white');
            break;
        case 'error':
            notification.classList.add('bg-red-500', 'text-white');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500', 'text-gray-900');
            break;
        default:
            notification.classList.add('bg-blue-500', 'text-white');
    }
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">
                <p class="font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Export functions for global use
window.AdminUtils = {
    showNotification,
    isValidEmail,
    isValidURL,
    debounce,
    validateForm,
    autoSaveForm,
    loadAutoSavedData
};
