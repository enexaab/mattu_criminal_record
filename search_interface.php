<?php
// search_interface.php
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: search_interface.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criminal Records Search - Clerk Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
        }
        
        .search-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .search-input {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid transparent;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .search-input:focus {
            background: white;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .filter-chip {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .filter-chip:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }
        
        .filter-chip.active {
            background: white;
            color: #667eea;
            font-weight: 600;
        }
        
        .results-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .table-row {
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .table-row:hover {
            background-color: #f8fafc;
            transform: translateX(4px);
        }
        
        .table-row:last-child {
            border-bottom: none;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            backdrop-filter: blur(2px);
        }
        
        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 4xl;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.show .modal-content {
            transform: scale(1);
        }
        
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-felony {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .badge-misdemeanor {
            background: #fef3c7;
            color: #d97706;
        }
        
        .badge-infraction {
            background: #dbeafe;
            color: #2563eb;
        }
        
        .badge-violation {
            background: #f3e8ff;
            color: #7c3aed;
        }
        
        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .pagination-btn:hover:not(:disabled) {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .pagination-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .search-stats {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 12px 16px;
            color: white;
            font-size: 14px;
            backdrop-filter: blur(10px);
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .advanced-search {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            backdrop-filter: blur(10px);
        }
        
        .date-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            padding: 8px 12px;
            color: #374151;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Criminal Records Database</h1>
                    <span class="ml-4 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Clerk - Read Only</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Clerk: Sarah Johnson (ID: CLK001)</span>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">Logout</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Interface -->
        <div class="search-container">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-white mb-2">Search Criminal Records</h2>
                <p class="text-white opacity-90">Enter search criteria to find records in the database</p>
            </div>
            
            <!-- Search Filters -->
            <div class="flex flex-wrap justify-center gap-3 mb-6">
                <button class="filter-chip active" data-filter="all" onclick="setSearchFilter('all')">
                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    All Records
                </button>
                <button class="filter-chip" data-filter="name" onclick="setSearchFilter('name')">
                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    By Name
                </button>
                <button class="filter-chip" data-filter="case" onclick="setSearchFilter('case')">
                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    By Case Number
                </button>
                <button class="filter-chip" data-filter="date" onclick="setSearchFilter('date')">
                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    By Date Range
                </button>
            </div>
            
            <!-- Main Search Input -->
            <div class="relative mb-4">
                <input type="text" id="searchInput" class="search-input w-full pl-12 pr-4" 
                       placeholder="Search by name, case number, or any keyword..." 
                       autocomplete="off">
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            
            <!-- Advanced Search (Date Range) -->
            <div id="advancedSearch" class="advanced-search" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white text-sm font-medium mb-2">From Date</label>
                        <input type="date" id="dateFrom" class="date-input w-full">
                    </div>
                    <div>
                        <label class="block text-white text-sm font-medium mb-2">To Date</label>
                        <input type="date" id="dateTo" class="date-input w-full">
                    </div>
                </div>
                <button onclick="performDateSearch()" class="mt-4 bg-white text-purple-600 px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                    Search Date Range
                </button>
            </div>
            
            <!-- Search Stats -->
            <div id="searchStats" class="search-stats" style="display: none;">
                <div class="flex items-center justify-between">
                    <span id="resultsCount">0 records found</span>
                    <span id="searchTime">Search completed in 0ms</span>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="relative">
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="loading-overlay" style="display: none;">
                <div class="text-center">
                    <div class="loading-spinner mx-auto mb-4"></div>
                    <p class="text-gray-600">Searching records...</p>
                </div>
            </div>
            
            <!-- Results Table -->
            <div id="resultsContainer" class="results-table">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Search Results</h3>
                    <p class="text-sm text-gray-600">Click on any record to view detailed information</p>
                </div>
                
                <div id="resultsTable">
                    <!-- Initial empty state -->
                    <div class="no-results">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Records Found</h3>
                        <p class="text-gray-500">Enter search criteria above to find criminal records</p>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div id="pagination" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between" style="display: none;">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-700">Show</span>
                        <select id="pageSize" class="border border-gray-300 rounded px-2 py-1 text-sm" onchange="changePageSize()">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-700">per page</span>
                    </div>
                    
                    <div class="flex items-center space-x-1">
                        <button id="prevPage" class="pagination-btn" onclick="changePage(-1)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div id="pageNumbers" class="flex space-x-1"></div>
                        <button id="nextPage" class="pagination-btn" onclick="changePage(1)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="text-sm text-gray-700">
                        <span id="pageInfo">Showing 1-25 of 100 results</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Record Detail Modal -->
    <div id="recordModal" class="modal-overlay">
        <div class="modal-content">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">Criminal Record Details</h2>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-2 flex items-center space-x-4">
                    <span id="modalRecordId" class="text-lg font-mono text-blue-600"></span>
                    <span id="modalOffenseType" class="badge"></span>
                </div>
            </div>
            
            <div id="modalContent" class="p-6">
                <!-- Modal content will be populated dynamically -->
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentPage = 1;
        let pageSize = 25;
        let totalRecords = 0;
        let currentFilter = 'all';
        let searchTimeout = null;
        let lastSearchQuery = '';

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            loadInitialData();
        });

        function setupEventListeners() {
            // Real-time search with debouncing
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(e.target.value);
                }, 300); // 300ms debounce
            });

            // Enter key search
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    performSearch(e.target.value);
                }
            });

            // Date range inputs
            document.getElementById('dateFrom').addEventListener('change', function() {
                if (currentFilter === 'date') {
                    performDateSearch();
                }
            });

            document.getElementById('dateTo').addEventListener('change', function() {
                if (currentFilter === 'date') {
                    performDateSearch();
                }
            });

            // Close modal on outside click
            document.getElementById('recordModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Escape key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        }

        function setSearchFilter(filter) {
            // Update active filter chip
            document.querySelectorAll('.filter-chip').forEach(chip => {
                chip.classList.remove('active');
            });
            document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
            
            currentFilter = filter;
            
            // Show/hide advanced search for date filter
            const advancedSearch = document.getElementById('advancedSearch');
            const searchInput = document.getElementById('searchInput');
            
            if (filter === 'date') {
                advancedSearch.style.display = 'block';
                searchInput.placeholder = 'Select date range below or search by keyword...';
            } else {
                advancedSearch.style.display = 'none';
                
                // Update placeholder based on filter
                switch(filter) {
                    case 'name':
                        searchInput.placeholder = 'Enter first name, last name, or full name...';
                        break;
                    case 'case':
                        searchInput.placeholder = 'Enter case number (e.g., CR-2024-001)...';
                        break;
                    default:
                        searchInput.placeholder = 'Search by name, case number, or any keyword...';
                }
            }
            
            // Perform search if there's existing input
            const query = searchInput.value.trim();
            if (query) {
                performSearch(query);
            }
        }

        function performSearch(query) {
            if (query === lastSearchQuery && currentPage === 1) {
                return; // Avoid duplicate searches
            }
            
            lastSearchQuery = query;
            currentPage = 1;
            
            if (!query.trim() && currentFilter !== 'date') {
                showEmptyState();
                return;
            }
            
            showLoading(true);
            
            const searchData = {
                query: query.trim(),
                filter: currentFilter,
                page: currentPage,
                pageSize: pageSize
            };
            
            // Simulate API call with realistic delay
            setTimeout(() => {
                const startTime = performance.now();
                
                // Simulate database search results
                const mockResults = generateMockResults(query, currentFilter);
                
                const endTime = performance.now();
                const searchTime = Math.round(endTime - startTime);
                
                displayResults(mockResults, searchTime);
                showLoading(false);
            }, Math.random() * 500 + 200); // 200-700ms delay
        }

        function performDateSearch() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            
            if (!dateFrom || !dateTo) {
                alert('Please select both start and end dates');
                return;
            }
            
            if (new Date(dateFrom) > new Date(dateTo)) {
                alert('Start date cannot be after end date');
                return;
            }
            
            showLoading(true);
            currentPage = 1;
            
            // Simulate date range search
            setTimeout(() => {
                const startTime = performance.now();
                const mockResults = generateDateRangeResults(dateFrom, dateTo);
                const endTime = performance.now();
                const searchTime = Math.round(endTime - startTime);
                
                displayResults(mockResults, searchTime);
                showLoading(false);
            }, Math.random() * 400 + 300);
        }

        function generateMockResults(query, filter) {
            // Simulate realistic search results based on query and filter
            const mockRecords = [
                {
                    id: 'CR-2024-001',
                    firstName: 'John',
                    lastName: 'Smith',
                    dateOfBirth: '1985-03-15',
                    offenseType: 'Felony',
                    offenseDate: '2024-01-15',
                    offenseDescription: 'Armed robbery at downtown bank',
                    status: 'Active',
                    arrestingOfficer: 'Officer Johnson #1234'
                },
                {
                    id: 'CR-2024-002',
                    firstName: 'Sarah',
                    lastName: 'Johnson',
                    dateOfBirth: '1992-07-22',
                    offenseType: 'Misdemeanor',
                    offenseDate: '2024-01-20',
                    offenseDescription: 'Shoplifting at retail store',
                    status: 'Closed',
                    arrestingOfficer: 'Officer Davis #5678'
                },
                {
                    id: 'CR-2024-003',
                    firstName: 'Michael',
                    lastName: 'Brown',
                    dateOfBirth: '1978-11-08',
                    offenseType: 'Infraction',
                    offenseDate: '2024-02-01',
                    offenseDescription: 'Speeding violation on Highway 101',
                    status: 'Pending',
                    arrestingOfficer: 'Officer Wilson #9012'
                },
                {
                    id: 'CR-2024-004',
                    firstName: 'Emily',
                    lastName: 'Davis',
                    dateOfBirth: '1990-05-12',
                    offenseType: 'Violation',
                    offenseDate: '2024-02-05',
                    offenseDescription: 'Public intoxication',
                    status: 'Active',
                    arrestingOfficer: 'Officer Martinez #3456'
                },
                {
                    id: 'CR-2024-005',
                    firstName: 'Robert',
                    lastName: 'Wilson',
                    dateOfBirth: '1983-09-30',
                    offenseType: 'Felony',
                    offenseDate: '2024-02-10',
                    offenseDescription: 'Drug trafficking charges',
                    status: 'Active',
                    arrestingOfficer: 'Officer Thompson #7890'
                }
            ];
            
            // Filter results based on search criteria
            let filteredResults = mockRecords;
            
            if (query) {
                const searchTerm = query.toLowerCase();
                filteredResults = mockRecords.filter(record => {
                    switch(filter) {
                        case 'name':
                            return record.firstName.toLowerCase().includes(searchTerm) ||
                                   record.lastName.toLowerCase().includes(searchTerm) ||
                                   `${record.firstName} ${record.lastName}`.toLowerCase().includes(searchTerm);
                        case 'case':
                            return record.id.toLowerCase().includes(searchTerm);
                        default:
                            return record.firstName.toLowerCase().includes(searchTerm) ||
                                   record.lastName.toLowerCase().includes(searchTerm) ||
                                   record.id.toLowerCase().includes(searchTerm) ||
                                   record.offenseDescription.toLowerCase().includes(searchTerm);
                    }
                });
            }
            
            totalRecords = filteredResults.length;
            
            // Simulate pagination
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            
            return {
                records: filteredResults.slice(startIndex, endIndex),
                total: totalRecords,
                page: currentPage,
                pageSize: pageSize
            };
        }

        function generateDateRangeResults(dateFrom, dateTo) {
            // Simulate date range search results
            const mockRecords = [
                {
                    id: 'CR-2024-001',
                    firstName: 'John',
                    lastName: 'Smith',
                    dateOfBirth: '1985-03-15',
                    offenseType: 'Felony',
                    offenseDate: '2024-01-15',
                    offenseDescription: 'Armed robbery at downtown bank',
                    status: 'Active',
                    arrestingOfficer: 'Officer Johnson #1234'
                },
                {
                    id: 'CR-2024-002',
                    firstName: 'Sarah',
                    lastName: 'Johnson',
                    dateOfBirth: '1992-07-22',
                    offenseType: 'Misdemeanor',
                    offenseDate: '2024-01-20',
                    offenseDescription: 'Shoplifting at retail store',
                    status: 'Closed',
                    arrestingOfficer: 'Officer Davis #5678'
                }
            ];
            
            // Filter by date range
            const filteredResults = mockRecords.filter(record => {
                const offenseDate = new Date(record.offenseDate);
                return offenseDate >= new Date(dateFrom) && offenseDate <= new Date(dateTo);
            });
            
            totalRecords = filteredResults.length;
            
            return {
                records: filteredResults,
                total: totalRecords,
                page: 1,
                pageSize: pageSize
            };
        }

        function displayResults(results, searchTime) {
            const resultsTable = document.getElementById('resultsTable');
            const searchStats = document.getElementById('searchStats');
            const pagination = document.getElementById('pagination');
            
            // Update search stats
            document.getElementById('resultsCount').textContent = `${results.total} record${results.total !== 1 ? 's' : ''} found`;
            document.getElementById('searchTime').textContent = `Search completed in ${searchTime}ms`;
            searchStats.style.display = 'block';
            
            if (results.records.length === 0) {
                showNoResults();
                pagination.style.display = 'none';
                return;
            }
            
            // Generate table HTML
            let tableHTML = `
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offense Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offense Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
            `;
            
            results.records.forEach(record => {
                const badgeClass = `badge-${record.offenseType.toLowerCase()}`;
                tableHTML += `
                    <tr class="table-row" onclick="openRecordModal('${record.id}')">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-blue-600">${record.id}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${record.firstName} ${record.lastName}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${formatDate(record.dateOfBirth)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge ${badgeClass}">${record.offenseType}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${formatDate(record.offenseDate)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(record.status)}">
                                ${record.status}
                            </span>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;
            
            resultsTable.innerHTML = tableHTML;
            
            // Update pagination
            updatePagination(results);
            pagination.style.display = 'flex';
        }

        function showNoResults() {
            const resultsTable = document.getElementById('resultsTable');
            resultsTable.innerHTML = `
                <div class="no-results">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Records Found</h3>
                    <p class="text-gray-500">No criminal records match your search criteria. Try adjusting your search terms.</p>
                </div>
            `;
        }

        function showEmptyState() {
            const resultsTable = document.getElementById('resultsTable');
            const searchStats = document.getElementById('searchStats');
            const pagination = document.getElementById('pagination');
            
            resultsTable.innerHTML = `
                <div class="no-results">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Ready to Search</h3>
                    <p class="text-gray-500">Enter search criteria above to find criminal records</p>
                </div>
            `;
            
            searchStats.style.display = 'none';
            pagination.style.display = 'none';
        }

        function showLoading(show) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = show ? 'flex' : 'none';
        }

        function updatePagination(results) {
            const totalPages = Math.ceil(results.total / pageSize);
            const pageNumbers = document.getElementById('pageNumbers');
            const pageInfo = document.getElementById('pageInfo');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            
            // Update page info
            const startRecord = (currentPage - 1) * pageSize + 1;
            const endRecord = Math.min(currentPage * pageSize, results.total);
            pageInfo.textContent = `Showing ${startRecord}-${endRecord} of ${results.total} results`;
            
            // Update navigation buttons
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
            
            // Generate page numbers
            let pagesHTML = '';
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                pagesHTML += `<button class="pagination-btn ${activeClass}" onclick="goToPage(${i})">${i}</button>`;
            }
            
            pageNumbers.innerHTML = pagesHTML;
        }

        function changePage(direction) {
            const newPage = currentPage + direction;
            const totalPages = Math.ceil(totalRecords / pageSize);
            
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                performSearch(document.getElementById('searchInput').value);
            }
        }

        function goToPage(page) {
            currentPage = page;
            performSearch(document.getElementById('searchInput').value);
        }

        function changePageSize() {
            pageSize = parseInt(document.getElementById('pageSize').value);
            currentPage = 1;
            performSearch(document.getElementById('searchInput').value);
        }

        function openRecordModal(recordId) {
            // Simulate fetching detailed record data
            const mockDetailedRecord = {
                id: recordId,
                firstName: 'John',
                middleName: 'Michael',
                lastName: 'Smith',
                dateOfBirth: '1985-03-15',
                gender: 'Male',
                nationality: 'American',
                address: {
                    street: '123 Main Street',
                    city: 'Springfield',
                    state: 'Illinois',
                    zipCode: '62701',
                    country: 'United States'
                },
                physical: {
                    height: 180,
                    weight: 75,
                    eyeColor: 'Brown',
                    hairColor: 'Black',
                    identifyingMarks: 'Scar on left forearm, tattoo on right shoulder'
                },
                offense: {
                    type: 'Felony',
                    date: '2024-01-15',
                    description: 'Armed robbery at downtown bank. Suspect entered First National Bank at approximately 2:30 PM with a concealed weapon and demanded cash from tellers. Security footage shows suspect wearing dark clothing and a mask.',
                    arrestingOfficer: 'Officer Johnson #1234',
                    location: 'First National Bank, 456 Oak Street, Springfield, IL'
                },
                status: 'Active',
                createdBy: 'Officer Johnson #1234',
                createdAt: '2024-01-15 15:45:00',
                lastModified: '2024-01-16 09:30:00'
            };
            
            displayRecordModal(mockDetailedRecord);
        }

        function displayRecordModal(record) {
            const modal = document.getElementById('recordModal');
            const modalContent = document.getElementById('modalContent');
            const modalRecordId = document.getElementById('modalRecordId');
            const modalOffenseType = document.getElementById('modalOffenseType');
            
            // Update modal header
            modalRecordId.textContent = record.id;
            modalOffenseType.textContent = record.offense.type;
            modalOffenseType.className = `badge badge-${record.offense.type.toLowerCase()}`;
            
            // Generate detailed content
            modalContent.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="info-card">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Personal Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Full Name:</span>
                                <span class="text-gray-900">${record.firstName} ${record.middleName || ''} ${record.lastName}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Date of Birth:</span>
                                <span class="text-gray-900">${formatDate(record.dateOfBirth)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Gender:</span>
                                <span class="text-gray-900">${record.gender}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Nationality:</span>
                                <span class="text-gray-900">${record.nationality}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Address Information -->
                    <div class="info-card">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            Address Information
                        </h3>
                        <div class="space-y-2">
                            <div class="text-gray-900">${record.address.street}</div>
                            <div class="text-gray-900">${record.address.city}, ${record.address.state} ${record.address.zipCode}</div>
                            <div class="text-gray-900">${record.address.country}</div>
                        </div>
                    </div>
                    
                    <!-- Physical Description -->
                    <div class="info-card">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Physical Description
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Height:</span>
                                <span class="text-gray-900">${record.physical.height} cm</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Weight:</span>
                                <span class="text-gray-900">${record.physical.weight} kg</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Eye Color:</span>
                                <span class="text-gray-900">${record.physical.eyeColor}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Hair Color:</span>
                                <span class="text-gray-900">${record.physical.hairColor}</span>
                            </div>
                        </div>
                        ${record.physical.identifyingMarks ? `
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <span class="font-medium text-gray-700">Identifying Marks:</span>
                                <p class="text-gray-900 mt-1">${record.physical.identifyingMarks}</p>
                            </div>
                        ` : ''}
                    </div>
                    
                    <!-- Offense Information -->
                    <div class="info-card">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Offense Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Offense Type:</span>
                                <span class="badge badge-${record.offense.type.toLowerCase()}">${record.offense.type}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Offense Date:</span>
                                <span class="text-gray-900">${formatDate(record.offense.date)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Arresting Officer:</span>
                                <span class="text-gray-900">${record.offense.arrestingOfficer}</span>
                            </div>
                            <div class="pt-3 border-t border-gray-200">
                                <span class="font-medium text-gray-700">Description:</span>
                                <p class="text-gray-900 mt-1">${record.offense.description}</p>
                            </div>
                            ${record.offense.location ? `
                                <div class="pt-3 border-t border-gray-200">
                                    <span class="font-medium text-gray-700">Location:</span>
                                    <p class="text-gray-900 mt-1">${record.offense.location}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- Record Metadata -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Status:</span>
                            <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(record.status)}">
                                ${record.status}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Created By:</span>
                            <span class="text-gray-900 ml-2">${record.createdBy}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Created:</span>
                            <span class="text-gray-900 ml-2">${formatDateTime(record.createdAt)}</span>
                        </div>
                    </div>
                </div>
            `;
            
            // Show modal
            modal.classList.add('show');
        }

        function closeModal() {
            const modal = document.getElementById('recordModal');
            modal.classList.remove('show');
        }

        function loadInitialData() {
            // Load some initial statistics or recent records
            showEmptyState();
        }

        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getStatusClass(status) {
            switch(status.toLowerCase()) {
                case 'active':
                    return 'bg-red-100 text-red-800';
                case 'closed':
                    return 'bg-gray-100 text-gray-800';
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800';
                default:
                    return 'bg-blue-100 text-blue-800';
            }
        }

        /*
        PHP Backend Code for Clerk Interface:

        // api/clerk_search.php
        <?php
        session_start();
        require_once '../config/database.php';
        require_once '../includes/auth.php';

        header('Content-Type: application/json');

        // Strict RBAC - Only allow clerks to perform READ operations
        if (!isLoggedIn() || getUserRole() !== 'clerk') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Clerk privileges required.']);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'];

        // Only allow GET requests for read-only access
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed. Read-only access only.']);
            exit;
        }

        $action = $_GET['action'] ?? '';

        switch($action) {
            case 'search':
                performSearch();
                break;
            case 'get_record':
                getRecordDetails();
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
        }

        function performSearch() {
            global $pdo;
            
            $query = trim($_GET['query'] ?? '');
            $filter = $_GET['filter'] ?? 'all';
            $page = max(1, intval($_GET['page'] ?? 1));
            $pageSize = min(100, max(10, intval($_GET['pageSize'] ?? 25)));
            $dateFrom = $_GET['dateFrom'] ?? '';
            $dateTo = $_GET['dateTo'] ?? '';
            
            $offset = ($page - 1) * $pageSize;
            
            try {
                // Build optimized query with proper indexing
                $baseQuery = "
                    SELECT 
                        cr.record_id,
                        cr.first_name,
                        cr.middle_name,
                        cr.last_name,
                        cr.date_of_birth,
                        cr.offense_type,
                        cr.offense_date,
                        cr.offense_description,
                        cr.created_at,
                        CASE 
                            WHEN cr.offense_date > DATE_SUB(NOW(), INTERVAL 1 YEAR) THEN 'Active'
                            WHEN cr.offense_date > DATE_SUB(NOW(), INTERVAL 2 YEAR) THEN 'Pending'
                            ELSE 'Closed'
                        END as status
                    FROM criminal_records cr
                ";
                
                $whereConditions = [];
                $params = [];
                
                // Apply search filters with optimized queries
                if (!empty($query)) {
                    switch($filter) {
                        case 'name':
                            $whereConditions[] = "(
                                cr.first_name LIKE ? OR 
                                cr.last_name LIKE ? OR 
                                CONCAT(cr.first_name, ' ', cr.last_name) LIKE ? OR
                                SOUNDEX(cr.first_name) = SOUNDEX(?) OR
                                SOUNDEX(cr.last_name) = SOUNDEX(?)
                            )";
                            $searchTerm = "%{$query}%";
                            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $query, $query]);
                            break;
                            
                        case 'case':
                            $whereConditions[] = "cr.record_id LIKE ?";
                            $params[] = "%{$query}%";
                            break;
                            
                        default: // 'all'
                            $whereConditions[] = "(
                                cr.first_name LIKE ? OR 
                                cr.last_name LIKE ? OR 
                                cr.record_id LIKE ? OR
                                cr.offense_description LIKE ?
                            )";
                            $searchTerm = "%{$query}%";
                            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
                    }
                }
                
                // Date range filter
                if (!empty($dateFrom) && !empty($dateTo)) {
                    $whereConditions[] = "cr.offense_date BETWEEN ? AND ?";
                    $params = array_merge($params, [$dateFrom, $dateTo]);
                }
                
                // Build final query
                $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
                $searchQuery = $baseQuery . $whereClause . " ORDER BY cr.offense_date DESC, cr.created_at DESC LIMIT ? OFFSET ?";
                $countQuery = "SELECT COUNT(*) as total FROM criminal_records cr " . $whereClause;
                
                // Execute count query
                $countStmt = $pdo->prepare($countQuery);
                $countStmt->execute($params);
                $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Execute search query
                $params[] = $pageSize;
                $params[] = $offset;
                $searchStmt = $pdo->prepare($searchQuery);
                $searchStmt->execute($params);
                $records = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Log search activity for audit
                logClerkActivity($_SESSION['user_id'], 'SEARCH', [
                    'query' => $query,
                    'filter' => $filter,
                    'results_count' => count($records)
                ]);
                
                echo json_encode([
                    'success' => true,
                    'records' => $records,
                    'total' => intval($totalRecords),
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'totalPages' => ceil($totalRecords / $pageSize)
                ]);
                
            } catch (Exception $e) {
                error_log("Search error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['error' => 'Search failed']);
            }
        }

        function getRecordDetails() {
            global $pdo;
            
            $recordId = $_GET['record_id'] ?? '';
            
            if (empty($recordId)) {
                http_response_code(400);
                echo json_encode(['error' => 'Record ID required']);
                return;
            }
            
            try {
                // Fetch complete record details with optimized query
                $query = "
                    SELECT 
                        cr.*,
                        u.first_name as created_by_first_name,
                        u.last_name as created_by_last_name,
                        u.badge_number as created_by_badge
                    FROM criminal_records cr
                    LEFT JOIN users u ON cr.created_by_user_id = u.id
                    WHERE cr.record_id = ?
                ";
                
                $stmt = $pdo->prepare($query);
                $stmt->execute([$recordId]);
                $record = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$record) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Record not found']);
                    return;
                }
                
                // Log record access for audit
                logClerkActivity($_SESSION['user_id'], 'VIEW_RECORD', [
                    'record_id' => $recordId
                ]);
                
                echo json_encode([
                    'success' => true,
                    'record' => $record
                ]);
                
            } catch (Exception $e) {
                error_log("Record fetch error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['error' => 'Failed to fetch record details']);
            }
        }

        function logClerkActivity($userId, $action, $details) {
            global $pdo;
            
            try {
                $query = "INSERT INTO clerk_activity_log (user_id, action, details, ip_address, user_agent, created_at) 
                         VALUES (?, ?, ?, ?, ?, NOW())";
                
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    $userId,
                    $action,
                    json_encode($details),
                    $_SERVER['REMOTE_ADDR'] ?? '',
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
            } catch (Exception $e) {
                error_log("Activity logging error: " . $e->getMessage());
            }
        }

        // includes/auth.php
       
        // Database Optimization Schema
        //-- Indexes for high-performance searches
        // CREATE INDEX idx_criminal_records_name_search ON criminal_records(first_name, last_name);
        // CREATE INDEX idx_criminal_records_record_id ON criminal_records(record_id);
        // CREATE INDEX idx_criminal_records_offense_date ON criminal_records(offense_date);
        // CREATE INDEX idx_criminal_records_created_at ON criminal_records(created_at);
        // CREATE INDEX idx_criminal_records_fulltext ON criminal_records(first_name, last_name, offense_description);
        // CREATE INDEX idx_criminal_records_soundex ON criminal_records(first_name, last_name) USING BTREE;

        // -- Clerk activity logging table
        // CREATE TABLE clerk_activity_log (
        //     id INT PRIMARY KEY AUTO_INCREMENT,
        //     user_id INT NOT NULL,
        //     action VARCHAR(50) NOT NULL,
        //     details JSON,
        //     ip_address VARCHAR(45),
        //     user_agent TEXT,
        //     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        //     FOREIGN KEY (user_id) REFERENCES users(id),
        //     INDEX idx_clerk_activity_user_id (user_id),
        //     INDEX idx_clerk_activity_created_at (created_at)
        // );

        // -- Database role-based security
        // CREATE USER 'clerk_user'@'localhost' IDENTIFIED BY 'secure_password';
        // GRANT SELECT ON police_records.criminal_records TO 'clerk_user'@'localhost';
        // GRANT SELECT ON police_records.users TO 'clerk_user'@'localhost';
        // GRANT INSERT ON police_records.clerk_activity_log TO 'clerk_user'@'localhost';
        // FLUSH PRIVILEGES;
      //  */
  