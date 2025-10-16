/**
 * Users Management JavaScript
 * Handles user-related functionality in the admin panel
 */

// Initialize users functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    initializeUsersPage();
});

/**
 * Initialize users page functionality
 */
function initializeUsersPage()
{
    initializeBulkActions();
    initializeDeleteConfirmation();
    initializeSelectAll();
    initializeUserApproval();
}

/**
 * Initialize bulk actions functionality
 */
function initializeBulkActions()
{
    // Add event listeners for bulk action buttons
    const bulkApproveBtn = document.querySelector('[data-action="bulk-approve"]');
    const bulkDeleteBtn = document.querySelector('[data-action="bulk-delete"]');

    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', bulkApprove);
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', bulkDelete);
    }

    // Update bulk actions visibility when checkboxes change
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsVisibility);
    });
}

/**
 * Initialize delete confirmation for individual delete buttons
 */
function initializeDeleteConfirmation()
{
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        const deleteBtn = form.querySelector('button[type="submit"]');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const confirmMessage = this.getAttribute('data-confirm');

                if (confirm(confirmMessage)) {
                    form.submit();
                }
            });
        }
    });
}

/**
 * Initialize select all functionality
 */
function initializeSelectAll()
{
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsVisibility();
        });
    }

    // Update select all checkbox when individual checkboxes change
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            const totalCount = rowCheckboxes.length;

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedCount === totalCount;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            }

            updateBulkActionsVisibility();
        });
    });
}

/**
 * Bulk approve selected users
 */
function bulkApprove()
{
    const selectedUsers = getSelectedUserIds();

    if (selectedUsers.length === 0) {
        window.showAlert('warning', 'Please select users to approve.');
        return;
    }

    if (!confirm(`Are you sure you want to approve ${selectedUsers.length} user(s) ? `)) {
        return;
    }

    // Create and submit form for bulk approval
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/users/bulk-approve';

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }

    // Add selected user IDs
    selectedUsers.forEach(userId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = userId;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

/**
 * Bulk delete selected users
 */
function bulkDelete()
{
    const selectedUsers = getSelectedUserIds();

    if (selectedUsers.length === 0) {
        window.showAlert('warning', 'Please select users to delete.');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${selectedUsers.length} user(s) ? This action cannot be undone.`)) {
        return;
    }

    // Create and submit form for bulk deletion
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/users/bulk-delete';

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }

    // Add method override for DELETE
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);

    // Add selected user IDs
    selectedUsers.forEach(userId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = userId;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

/**
 * Get selected user IDs
 * @returns {Array} Array of selected user IDs
 */
function getSelectedUserIds()
{
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    return Array.from(checkedBoxes).map(checkbox => checkbox.value);
}

/**
 * Update bulk actions visibility based on selected items
 */
function updateBulkActionsVisibility()
{
    const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;
    const bulkActions = document.getElementById('bulkActions');
    const selectedCountElement = document.querySelector('.selected-count');

    if (bulkActions) {
        if (selectedCount > 0) {
            bulkActions.style.display = 'flex';
        } else {
            bulkActions.style.display = 'none';
        }
    }

    if (selectedCountElement) {
        selectedCountElement.textContent = selectedCount;
    }
}

/**
 * Show user details in modal (if needed)
 * @param {number} userId - User ID
 */
function showUserDetails(userId)
{
    // This function can be implemented if user details modal is needed
    console.log('Show user details for ID:', userId);
}

/**
 * Export selected users
 */
function exportSelectedUsers()
{
    const selectedUsers = getSelectedUserIds();

    if (selectedUsers.length === 0) {
        window.showAlert('warning', 'Please select users to export.');
        return;
    }

    // Create export URL with selected user IDs
    const exportUrl = new URL('/admin/users/export', window.location.origin);
    selectedUsers.forEach(userId => {
        exportUrl.searchParams.append('user_ids[]', userId);
    });

    // Open export URL
    window.open(exportUrl.toString(), '_blank');
}

/**
 * Initialize user approval confirmation
 */
function initializeUserApproval()
{
    const approveBtn = document.querySelector('[data-confirm="approve-user"]');
    if (approveBtn) {
        approveBtn.addEventListener('click', function (e) {
            const confirmMessage = this.getAttribute('data-confirm-message') || 'Are you sure you want to approve this user?';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    }
}

/**
 * Refresh balance function for balance page
 */
window.refreshBalance = function () {
    // Show loading state
    const refreshBtn = document.querySelector('[data-action="refresh-balance"]');
    if (refreshBtn) {
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        refreshBtn.disabled = true;

        // Simulate refresh (reload page for now)
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
};

/**
 * Initialize balance management functionality
 */
function initializeBalanceManagement()
{
    // Add balance form submission
    const addBalanceForm = document.querySelector('#addBalanceModal form');
    if (addBalanceForm) {
        addBalanceForm.addEventListener('submit', handleAddBalance);
    }

    // Deduct balance form submission
    const deductBalanceForm = document.querySelector('#deductBalanceModal form');
    if (deductBalanceForm) {
        deductBalanceForm.addEventListener('submit', handleDeductBalance);
    }

    // Refresh balance button
    const refreshBtn = document.querySelector('[data-action="refresh-balance"]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshBalance);
    }
}

/**
 * Handle add balance form submission
 */
function handleAddBalance(e)
{
    const form = e.target;
    const amount = form.querySelector('[name="amount"]').value;

    if (!amount || amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount');
        return false;
    }

    // Show loading state
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    submitBtn.disabled = true;

    // Form will submit normally
}

/**
 * Handle deduct balance form submission
 */
function handleDeductBalance(e)
{
    const form = e.target;
    const amount = form.querySelector('[name="amount"]').value;
    const note = form.querySelector('[name="note"]').value;

    if (!amount || amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount');
        return false;
    }

    if (!note.trim()) {
        e.preventDefault();
        alert('Please provide a reason for deducting balance');
        return false;
    }

    // Show loading state
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deducting...';
    submitBtn.disabled = true;

    // Form will submit normally
}

// Initialize balance management when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    initializeBalanceManagement();
});

// Export functions for global access if needed
window.UsersManager = {
    bulkApprove,
    bulkDelete,
    showUserDetails,
    exportSelectedUsers,
    initializeBalanceManagement
};