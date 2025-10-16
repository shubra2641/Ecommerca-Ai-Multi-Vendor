/**
 * Languages Management JavaScript
 * Handles language-related functionality in the admin panel
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeLanguagesPage();
});

function initializeLanguagesPage() {
    // Initialize bulk actions
    initializeBulkActions();
    
    // Initialize individual language actions
    initializeLanguageActions();
    
    // Initialize select all functionality
    initializeSelectAll();
}

// Bulk Actions Management
function initializeBulkActions() {
    // Bulk activate button
    const bulkActivateBtn = document.querySelector('[data-action="bulk-activate"]');
    if (bulkActivateBtn) {
        bulkActivateBtn.addEventListener('click', bulkActivate);
    }
    
    // Bulk deactivate button
    const bulkDeactivateBtn = document.querySelector('[data-action="bulk-deactivate"]');
    if (bulkDeactivateBtn) {
        bulkDeactivateBtn.addEventListener('click', bulkDeactivate);
    }
    
    // Bulk delete button
    const bulkDeleteBtn = document.querySelector('[data-action="bulk-delete"]');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', bulkDelete);
    }
    
    // Refresh translations button
    const refreshBtn = document.querySelector('[data-action="refresh-translations"]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshTranslations);
    }
}

// Individual Language Actions
function initializeLanguageActions() {
    // Set default language buttons
    const setDefaultBtns = document.querySelectorAll('[data-action="set-default"]');
    setDefaultBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const languageId = this.getAttribute('data-language-id');
            setDefaultLanguage(languageId);
        });
    });
    
    // Delete translation buttons
    const deleteTranslationBtns = document.querySelectorAll('[data-action="delete-translation"]');
    deleteTranslationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const translationKey = this.getAttribute('data-translation-key');
            deleteTranslation(translationKey);
        });
    });
}

// Select All Functionality
function initializeSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="language_ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsVisibility();
        });
    }
    
    // Individual checkboxes
    const languageCheckboxes = document.querySelectorAll('input[name="language_ids[]"]');
    languageCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsVisibility);
    });
}

// Bulk Actions Functions
function bulkActivate() {
    const selectedIds = getSelectedLanguageIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one language to activate.');
        return;
    }
    
    if (confirm(`Are you sure you want to activate ${selectedIds.length} language(s)?`)) {
        // Create and submit form
        const form = createBulkActionForm('activate', selectedIds);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkDeactivate() {
    const selectedIds = getSelectedLanguageIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one language to deactivate.');
        return;
    }
    
    if (confirm(`Are you sure you want to deactivate ${selectedIds.length} language(s)?`)) {
        // Create and submit form
        const form = createBulkActionForm('deactivate', selectedIds);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkDelete() {
    const selectedIds = getSelectedLanguageIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one language to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} language(s)? This action cannot be undone.`)) {
        // Create and submit form
        const form = createBulkActionForm('delete', selectedIds);
        document.body.appendChild(form);
        form.submit();
    }
}

function refreshTranslations() {
    // Show loading state
    const refreshBtn = document.querySelector('[data-action="refresh-translations"]');
    if (refreshBtn) {
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        refreshBtn.disabled = true;
        
        // Make AJAX request to refresh cache
        fetch('/admin/languages/refresh-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof window.showAlert === 'function') {
                    window.showAlert('success', data.message || 'Translation cache refreshed successfully!');
                }
            } else {
                if (typeof window.showAlert === 'function') {
                    window.showAlert('error', data.message || 'Failed to refresh translation cache');
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing translations:', error);
            if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Failed to refresh translation cache');
            }
        })
        .finally(() => {
            // Restore button state
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        });
    }
}

function setDefaultLanguage(languageId) {
    if (confirm('Are you sure you want to set this as the default language?')) {
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/languages/${languageId}/set-default`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        // Add method spoofing for PUT/PATCH
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Helper Functions
function getSelectedLanguageIds() {
    const checkboxes = document.querySelectorAll('input[name="language_ids[]"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function updateBulkActionsVisibility() {
    const selectedCount = getSelectedLanguageIds().length;
    const bulkActions = document.querySelector('.bulk-actions');
    if (bulkActions) {
        bulkActions.style.display = selectedCount > 0 ? 'block' : 'none';
    }
}

function createBulkActionForm(action, ids) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/languages/bulk-${action}`;
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }
    
    // Add language IDs
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'language_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    return form;
}

// Export functions for global access if needed
window.LanguagesManager = {
    bulkActivate,
    bulkDeactivate,
    bulkDelete,
    refreshTranslations,
    setDefaultLanguage
};

// Delete translation function
function deleteTranslation(key) {
    if (confirm(`Are you sure you want to delete the translation for "${key}"?`)) {
        // Find and remove the translation item
        const translationItems = document.querySelectorAll('.translation-item');
        translationItems.forEach(item => {
            const label = item.querySelector('.translation-key label');
            if (label && label.textContent.trim() === key) {
                item.remove();
            }
        });
    }
}

// Global functions for backward compatibility
window.bulkActivate = bulkActivate;
window.bulkDeactivate = bulkDeactivate;
window.bulkDelete = bulkDelete;
window.refreshTranslations = refreshTranslations;
window.setDefaultLanguage = setDefaultLanguage;
window.deleteTranslation = deleteTranslation;