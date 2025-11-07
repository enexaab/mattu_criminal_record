<?php
// add_criminal.php
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System - Administrator Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .status-active {
            background-color: #10b981;
            color: white;
        }
        
        .status-inactive {
            background-color: #ef4444;
            color: white;
        }
        
        .role-administrator {
            background-color: #8b5cf6;
            color: white;
        }
        
        .role-police {
            background-color: #3b82f6;
            color: white;
        }
        
        .role-clerk {
            background-color: #f59e0b;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">User Management System</h1>
                    <span class="ml-4 px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">Administrator</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, Admin</span>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">Logout</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Manage User Accounts</h2>
            <p class="text-gray-600">Create, edit, and manage user accounts with role-based access control</p>
        </div>

        <!-- Controls Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Search and Filter -->
                <div class="flex flex-col sm:flex-row gap-4 flex-1">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search users..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <select id="roleFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Roles</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Police Officer">Police Officer</option>
                        <option value="Clerk">Clerk</option>
                    </select>
                    
                    <select id="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                <!-- Add User Button -->
                <button id="addUserBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New User
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('id')">
                                ID
                                <svg class="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('name')">
                                Name
                                <svg class="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('role')">
                                Role
                                <svg class="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Users will be populated here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prevPageMobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                    <button id="nextPageMobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span id="showingStart" class="font-medium">1</span> to <span id="showingEnd" class="font-medium">10</span> of <span id="totalUsers" class="font-medium">47</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" id="pagination">
                            <!-- Pagination buttons will be generated here -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 fade-in">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New User</h3>
            </div>
            
            <form id="userForm" class="px-6 py-4">
                <div class="space-y-4">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                        <input type="text" id="username" name="username" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div id="passwordField">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters with uppercase, lowercase, number, and special character</p>
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role/Rank *</label>
                        <select id="role" name="role" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Role</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Police Officer">Police Officer</option>
                            <option value="Clerk">Clerk</option>
                        </select>
                    </div>
                    
                    <div id="statusField" style="display: none;">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 fade-in">
            <div class="px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Action</h3>
                <p id="confirmMessage" class="text-gray-600 mb-4">Are you sure you want to perform this action?</p>
                <div class="flex justify-end space-x-3">
                    <button id="confirmCancel" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button id="confirmAction" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data - In real implementation, this would come from PHP/MySQL
        let users = [
            { id: 1, firstName: 'John', lastName: 'Smith', email: 'john.smith@police.gov', username: 'jsmith', role: 'Administrator', status: 'Active', created: '2024-01-15' },
            { id: 2, firstName: 'Sarah', lastName: 'Johnson', email: 'sarah.johnson@police.gov', username: 'sjohnson', role: 'Police Officer', status: 'Active', created: '2024-01-20' },
            { id: 3, firstName: 'Mike', lastName: 'Davis', email: 'mike.davis@police.gov', username: 'mdavis', role: 'Clerk', status: 'Active', created: '2024-02-01' },
            { id: 4, firstName: 'Lisa', lastName: 'Wilson', email: 'lisa.wilson@police.gov', username: 'lwilson', role: 'Police Officer', status: 'Inactive', created: '2024-02-10' },
            { id: 5, firstName: 'Robert', lastName: 'Brown', email: 'robert.brown@police.gov', username: 'rbrown', role: 'Clerk', status: 'Active', created: '2024-02-15' }
        ];

        let filteredUsers = [...users];
        let currentPage = 1;
        let usersPerPage = 10;
        let sortColumn = '';
        let sortDirection = 'asc';
        let editingUserId = null;

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            renderUsers();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', filterUsers);
            document.getElementById('roleFilter').addEventListener('change', filterUsers);
            document.getElementById('statusFilter').addEventListener('change', filterUsers);

            // Modal controls
            document.getElementById('addUserBtn').addEventListener('click', openAddUserModal);
            document.getElementById('cancelBtn').addEventListener('click', closeModal);
            document.getElementById('userForm').addEventListener('submit', handleUserSubmit);

            // Confirmation modal
            document.getElementById('confirmCancel').addEventListener('click', closeConfirmModal);

            // Close modals when clicking outside
            document.getElementById('userModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
            document.getElementById('confirmModal').addEventListener('click', function(e) {
                if (e.target === this) closeConfirmModal();
            });
        }

        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;

            filteredUsers = users.filter(user => {
                const matchesSearch = !searchTerm || 
                    user.firstName.toLowerCase().includes(searchTerm) ||
                    user.lastName.toLowerCase().includes(searchTerm) ||
                    user.email.toLowerCase().includes(searchTerm) ||
                    user.username.toLowerCase().includes(searchTerm);

                const matchesRole = !roleFilter || user.role === roleFilter;
                const matchesStatus = !statusFilter || user.status === statusFilter;

                return matchesSearch && matchesRole && matchesStatus;
            });

            currentPage = 1;
            renderUsers();
        }

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            filteredUsers.sort((a, b) => {
                let aVal = a[column];
                let bVal = b[column];

                if (column === 'name') {
                    aVal = a.firstName + ' ' + a.lastName;
                    bVal = b.firstName + ' ' + b.lastName;
                }

                if (typeof aVal === 'string') {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }

                if (sortDirection === 'asc') {
                    return aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
                } else {
                    return aVal > bVal ? -1 : aVal < bVal ? 1 : 0;
                }
            });

            renderUsers();
        }

        function renderUsers() {
            const tbody = document.getElementById('usersTableBody');
            const startIndex = (currentPage - 1) * usersPerPage;
            const endIndex = startIndex + usersPerPage;
            const pageUsers = filteredUsers.slice(startIndex, endIndex);

            tbody.innerHTML = pageUsers.map(user => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.firstName} ${user.lastName}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${user.email}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${user.username}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full role-${user.role.toLowerCase().replace(' ', '')}">${user.role}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full status-${user.status.toLowerCase()}">${user.status}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${formatDate(user.created)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 transition-colors">Edit</button>
                        <button onclick="toggleUserStatus(${user.id})" class="text-${user.status === 'Active' ? 'red' : 'green'}-600 hover:text-${user.status === 'Active' ? 'red' : 'green'}-900 transition-colors">
                            ${user.status === 'Active' ? 'Deactivate' : 'Reactivate'}
                        </button>
                    </td>
                </tr>
            `).join('');

            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
            const pagination = document.getElementById('pagination');
            
            // Update showing text
            const startIndex = (currentPage - 1) * usersPerPage + 1;
            const endIndex = Math.min(currentPage * usersPerPage, filteredUsers.length);
            document.getElementById('showingStart').textContent = startIndex;
            document.getElementById('showingEnd').textContent = endIndex;
            document.getElementById('totalUsers').textContent = filteredUsers.length;

            // Generate pagination buttons
            let paginationHTML = '';
            
            // Previous button
            paginationHTML += `
                <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} 
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${currentPage === 1 ? 'cursor-not-allowed opacity-50' : ''}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    paginationHTML += `
                        <button onclick="changePage(${i})" 
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${i === currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'}">
                            ${i}
                        </button>
                    `;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    paginationHTML += `
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                    `;
                }
            }

            // Next button
            paginationHTML += `
                <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} 
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${currentPage === totalPages ? 'cursor-not-allowed opacity-50' : ''}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;

            pagination.innerHTML = paginationHTML;
        }

        function changePage(page) {
            const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                renderUsers();
            }
        }

        function openAddUserModal() {
            editingUserId = null;
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('submitBtn').textContent = 'Create User';
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('statusField').style.display = 'none';
            document.getElementById('userForm').reset();
            document.getElementById('userModal').classList.add('show');
        }

        function editUser(userId) {
            const user = users.find(u => u.id === userId);
            if (!user) return;

            editingUserId = userId;
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('submitBtn').textContent = 'Update User';
            document.getElementById('passwordField').style.display = 'none';
            document.getElementById('statusField').style.display = 'block';

            // Populate form
            document.getElementById('firstName').value = user.firstName;
            document.getElementById('lastName').value = user.lastName;
            document.getElementById('email').value = user.email;
            document.getElementById('username').value = user.username;
            document.getElementById('role').value = user.role;
            document.getElementById('status').value = user.status;

            document.getElementById('userModal').classList.add('show');
        }

        function handleUserSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const userData = {
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                username: formData.get('username'),
                role: formData.get('role'),
                status: formData.get('status') || 'Active'
            };

            // Validation
            if (!validateUserData(userData)) return;

            if (editingUserId) {
                // Update existing user
                const userIndex = users.findIndex(u => u.id === editingUserId);
                if (userIndex !== -1) {
                    users[userIndex] = { ...users[userIndex], ...userData };
                    showNotification('User updated successfully!', 'success');
                }
            } else {
                // Create new user
                const password = formData.get('password');
                if (!validatePassword(password)) return;

                const newUser = {
                    id: Math.max(...users.map(u => u.id)) + 1,
                    ...userData,
                    created: new Date().toISOString().split('T')[0]
                };
                users.push(newUser);
                showNotification('User created successfully!', 'success');
            }

            closeModal();
            filterUsers(); // Refresh the display
        }

        function validateUserData(userData) {
            // Check for required fields
            if (!userData.firstName || !userData.lastName || !userData.email || !userData.username || !userData.role) {
                showNotification('Please fill in all required fields.', 'error');
                return false;
            }

            // Check email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(userData.email)) {
                showNotification('Please enter a valid email address.', 'error');
                return false;
            }

            // Check for unique email and username (excluding current user if editing)
            const existingUser = users.find(u => 
                (u.email === userData.email || u.username === userData.username) && 
                u.id !== editingUserId
            );
            if (existingUser) {
                showNotification('Email or username already exists.', 'error');
                return false;
            }

            return true;
        }

        function validatePassword(password) {
            if (!password) {
                showNotification('Password is required.', 'error');
                return false;
            }

            // Password strength validation
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(password)) {
                showNotification('Password must be at least 8 characters with uppercase, lowercase, number, and special character.', 'error');
                return false;
            }

            return true;
        }

        function toggleUserStatus(userId) {
            const user = users.find(u => u.id === userId);
            if (!user) return;

            const action = user.status === 'Active' ? 'deactivate' : 'reactivate';
            const message = `Are you sure you want to ${action} ${user.firstName} ${user.lastName}?`;

            showConfirmModal(message, () => {
                user.status = user.status === 'Active' ? 'Inactive' : 'Active';
                showNotification(`User ${action}d successfully!`, 'success');
                filterUsers(); // Refresh the display
            });
        }

        function showConfirmModal(message, onConfirm) {
            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmAction').onclick = () => {
                onConfirm();
                closeConfirmModal();
            };
            document.getElementById('confirmModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('userModal').classList.remove('show');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('show');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} fade-in`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Simulated PHP backend functions (in real implementation, these would be AJAX calls to PHP endpoints)
        
        /*
        PHP Backend Code Structure:

        // config/database.php
        <?php
        $host = 'localhost';
        $dbname = 'user_management';
        $username = 'your_username';
        $password = 'your_password';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
        ?>

        // api/users.php
        <?php
        require_once '../config/database.php';

        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];

        switch($method) {
            case 'GET':
                getUsers();
                break;
            case 'POST':
                createUser();
                break;
            case 'PUT':
                updateUser();
                break;
            case 'PATCH':
                toggleUserStatus();
                break;
        }

        function getUsers() {
            global $pdo;
            
            $search = $_GET['search'] ?? '';
            $role = $_GET['role'] ?? '';
            $status = $_GET['status'] ?? '';
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT id, first_name, last_name, email, username, role, status, created_at 
                    FROM users WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR username LIKE ?)";
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            }
            
            if ($role) {
                $sql .= " AND role = ?";
                $params[] = $role;
            }
            
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $countSql = "SELECT COUNT(*) FROM users WHERE 1=1";
            $countParams = array_slice($params, 0, -2); // Remove limit and offset
            
            if ($search) {
                $countSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR username LIKE ?)";
            }
            if ($role) {
                $countSql .= " AND role = ?";
            }
            if ($status) {
                $countSql .= " AND status = ?";
            }
            
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($countParams);
            $total = $countStmt->fetchColumn();
            
            echo json_encode([
                'users' => $users,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ]);
        }

        function createUser() {
            global $pdo;
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            if (!$input['first_name'] || !$input['last_name'] || !$input['email'] || 
                !$input['username'] || !$input['password'] || !$input['role']) {
                http_response_code(400);
                echo json_encode(['error' => 'All fields are required']);
                return;
            }
            
            // Check for existing email/username
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $checkStmt->execute([$input['email'], $input['username']]);
            if ($checkStmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'Email or username already exists']);
                return;
            }
            
            // Hash password
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $pdo->prepare("
                INSERT INTO users (first_name, last_name, email, username, password_hash, role, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'Active', NOW())
            ");
            
            $stmt->execute([
                $input['first_name'],
                $input['last_name'],
                $input['email'],
                $input['username'],
                $hashedPassword,
                $input['role']
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        }

        function updateUser() {
            global $pdo;
            
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['id'];
            
            // Check for existing email/username (excluding current user)
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?");
            $checkStmt->execute([$input['email'], $input['username'], $userId]);
            if ($checkStmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'Email or username already exists']);
                return;
            }
            
            $stmt = $pdo->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, email = ?, username = ?, role = ?, status = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $input['first_name'],
                $input['last_name'],
                $input['email'],
                $input['username'],
                $input['role'],
                $input['status'],
                $userId
            ]);
            
            echo json_encode(['success' => true]);
        }

        function toggleUserStatus() {
            global $pdo;
            
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['id'];
            
            $stmt = $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'Active' THEN 'Inactive' ELSE 'Active' END WHERE id = ?");
            $stmt->execute([$userId]);
            
            echo json_encode(['success' => true]);
        }
        ?>

        // Database Schema (MySQL)
        CREATE TABLE users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('Administrator', 'Police Officer', 'Clerk') NOT NULL,
            status ENUM('Active', 'Inactive') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );

        CREATE INDEX idx_users_email ON users(email);
        CREATE INDEX idx_users_username ON users(username);
        CREATE INDEX idx_users_role ON users(role);
        CREATE INDEX idx_users_status ON users(status);
        */
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'985b58b7d0477893',t:'MTc1ODk3OTczMy4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
