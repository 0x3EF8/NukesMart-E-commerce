// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin functionality
    initAdminDashboard();
});

function initAdminDashboard() {
    // Initialize dropdowns
    initDropdowns();
    
    // Initialize search functionality
    initSearch();
    
    // Initialize notifications
    initNotifications();
    
    // Initialize responsive sidebar
    initResponsiveSidebar();
    
    // Initialize charts (if they exist)
    initCharts();
    
    // Initialize data tables
    initDataTables();
}

// Dropdown functionality
function initDropdowns() {
    const dropdowns = document.querySelectorAll('.profile-dropdown');
    
    dropdowns.forEach(dropdown => {
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        const dropdownBtn = dropdown.querySelector('.dropdown-btn');
        
        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.profile-dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
}

// Search functionality
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            
            // You can implement search functionality here
            // For now, we'll just log the search query
            console.log('Searching for:', query);
        });
        
        // Add search icon click functionality
        const searchIcon = document.querySelector('.search-icon');
        if (searchIcon) {
            searchIcon.addEventListener('click', function() {
                searchInput.focus();
            });
        }
    }
}

// Notifications functionality
function initNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            // You can implement notification panel here
            console.log('Notifications clicked');
        });
    }
}

// Responsive sidebar functionality
function initResponsiveSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!e.target.closest('.admin-sidebar') && !e.target.closest('.sidebar-toggle')) {
                sidebar.classList.remove('open');
            }
        }
    });
}

// Charts initialization
function initCharts() {
    // This function will be called if charts exist on the page
    // Chart.js charts are initialized in the specific pages
    console.log('Charts initialized');
}

// Data tables functionality
function initDataTables() {
    const tables = document.querySelectorAll('.admin-table');
    
    tables.forEach(table => {
        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sortable]');
        
        headers.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.dataset.column;
                const currentOrder = this.dataset.order || 'asc';
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
                
                // Update header order indicator
                this.dataset.order = newOrder;
                
                // Sort table
                sortTable(table, column, newOrder);
            });
        });
    });
}

// Table sorting function
function sortTable(table, column, order) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.querySelector(`td[data-${column}]`).textContent;
        const bValue = b.querySelector(`td[data-${column}]`).textContent;
        
        if (order === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });
    
    // Reorder rows
    rows.forEach(row => tbody.appendChild(row));
}

// Utility functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    // Insert at the top of main content
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

function showLoading(element) {
    element.classList.add('loading');
    element.innerHTML = '<div class="loading-spinner"></div>';
}

function hideLoading(element, originalContent) {
    element.classList.remove('loading');
    element.innerHTML = originalContent;
}

// AJAX utility functions
function makeRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    };
    
    const requestOptions = { ...defaultOptions, ...options };
    
    return fetch(url, requestOptions)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('Request failed:', error);
            showAlert('An error occurred. Please try again.', 'error');
            throw error;
        });
}

// Form handling
function handleFormSubmit(form, successCallback = null) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        
        // Show loading state
        showLoading(submitBtn);
        
        // Make AJAX request
        makeRequest(form.action, {
            method: form.method,
            body: formData
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message || 'Operation completed successfully!', 'success');
                if (successCallback) {
                    successCallback(data);
                }
            } else {
                showAlert(data.message || 'Operation failed!', 'error');
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            hideLoading(submitBtn, originalBtnText);
        });
    });
}

// Modal functionality
function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            
            if (modal) {
                modal.classList.add('show');
                document.body.classList.add('modal-open');
            }
        });
    });
    
    // Close modal functionality
    const modalCloses = document.querySelectorAll('.modal-close, .modal-overlay');
    
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        });
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                openModal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        }
    });
}

// Export functions for use in other scripts
window.AdminDashboard = {
    showAlert,
    makeRequest,
    handleFormSubmit,
    initModals
};
