<?php
// add_criminal.php
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: criminal_record.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Criminal Record - Police Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
        }
        
        .form-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            padding: 24px;
        }
        
        .file-drop-zone {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-drop-zone:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }
        
        .file-drop-zone.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .progress-bar {
            width: 0%;
            height: 4px;
            background-color: #3b82f6;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background-color: #10b981;
        }
        
        .notification.error {
            background-color: #ef4444;
        }
        
        .duplicate-warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }
        
        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Criminal Records Management</h1>
                    <span class="ml-4 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">Police Officer</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Officer: John Smith (Badge #1234)</span>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">Logout</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Add New Criminal Record</h2>
                    <p class="text-gray-600">Enter complete information for the new criminal record</p>
                </div>
                <button onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Records
                </button>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                <span>Form Progress</span>
                <span id="progressText">0% Complete</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progressBar" class="progress-bar"></div>
            </div>
        </div>

        <!-- Duplicate Check Results -->
        <div id="duplicateWarning" class="duplicate-warning" style="display: none;">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-yellow-800">Potential Duplicate Records Found</h4>
                    <div id="duplicateResults" class="mt-2 text-sm text-yellow-700"></div>
                    <p class="mt-2 text-sm text-yellow-700">Please review these records before proceeding. You can still continue if this is a different person.</p>
                </div>
            </div>
        </div>

        <!-- Criminal Record Form -->
        <form id="criminalRecordForm" enctype="multipart/form-data">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrfToken" value="abc123def456ghi789">
            
            <!-- Personal Information Section -->
            <div class="form-section">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Personal Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="middleName" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                        <input type="text" id="middleName" name="middleName" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="dateOfBirth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                        <select id="gender" name="gender" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                        <input type="text" id="nationality" name="nationality" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Address Information Section -->
            <div class="form-section">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Address Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="streetAddress" class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                        <input type="text" id="streetAddress" name="streetAddress" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input type="text" id="city" name="city" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Province *</label>
                        <input type="text" id="state" name="state" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="zipCode" class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal Code</label>
                        <input type="text" id="zipCode" name="zipCode" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                        <input type="text" id="country" name="country" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Physical Description Section -->
            <div class="form-section">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Physical Description
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm) *</label>
                        <input type="number" id="height" name="height" required min="100" max="250"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg) *</label>
                        <input type="number" id="weight" name="weight" required min="30" max="300"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="eyeColor" class="block text-sm font-medium text-gray-700 mb-2">Eye Color *</label>
                        <select id="eyeColor" name="eyeColor" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Eye Color</option>
                            <option value="Brown">Brown</option>
                            <option value="Blue">Blue</option>
                            <option value="Green">Green</option>
                            <option value="Hazel">Hazel</option>
                            <option value="Gray">Gray</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="hairColor" class="block text-sm font-medium text-gray-700 mb-2">Hair Color *</label>
                        <select id="hairColor" name="hairColor" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Hair Color</option>
                            <option value="Black">Black</option>
                            <option value="Brown">Brown</option>
                            <option value="Blonde">Blonde</option>
                            <option value="Red">Red</option>
                            <option value="Gray">Gray</option>
                            <option value="Bald">Bald</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="identifyingMarks" class="block text-sm font-medium text-gray-700 mb-2">Identifying Marks/Scars/Tattoos</label>
                    <textarea id="identifyingMarks" name="identifyingMarks" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describe any visible scars, tattoos, birthmarks, or other identifying features..."></textarea>
                </div>
            </div>

            <!-- Photo Upload Section -->
            <div class="form-section">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Photo Upload
                </h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <div id="photoDropZone" class="file-drop-zone">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-700 mb-2">Drop photo here or click to browse</p>
                            <p class="text-sm text-gray-500">JPG, PNG, or GIF up to 5MB</p>
                            <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png,image/gif" class="hidden">
                        </div>
                        
                        <div id="uploadProgress" class="mt-4" style="display: none;">
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                <span>Uploading...</span>
                                <span id="uploadPercent">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="uploadBar" class="progress-bar"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="photoPreview" class="flex items-center justify-center" style="display: none;">
                        <div class="text-center">
                            <img id="previewImage" class="photo-preview mx-auto mb-4" alt="Photo preview">
                            <button type="button" id="removePhoto" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove Photo</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offense Information Section -->
            <div class="form-section">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Offense Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="offenseType" class="block text-sm font-medium text-gray-700 mb-2">Offense Type *</label>
                        <select id="offenseType" name="offenseType" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Offense Type</option>
                            <option value="Felony">Felony</option>
                            <option value="Misdemeanor">Misdemeanor</option>
                            <option value="Infraction">Infraction</option>
                            <option value="Violation">Violation</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="offenseDate" class="block text-sm font-medium text-gray-700 mb-2">Offense Date *</label>
                        <input type="date" id="offenseDate" name="offenseDate" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="offenseDescription" class="block text-sm font-medium text-gray-700 mb-2">Offense Description *</label>
                    <textarea id="offenseDescription" name="offenseDescription" rows="4" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Provide detailed description of the offense, including circumstances, location, and any relevant details..."></textarea>
                </div>
                
                <div class="mt-6">
                    <label for="arrestingOfficer" class="block text-sm font-medium text-gray-700 mb-2">Arresting Officer</label>
                    <input type="text" id="arrestingOfficer" name="arrestingOfficer" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Officer name and badge number">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6">
                <button type="button" id="checkDuplicates" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Check for Duplicates
                </button>
                
                <div class="flex gap-4">
                    <button type="button" onclick="saveDraft()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Save as Draft
                    </button>
                    <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <span>Create Criminal Record</span>
                        <div id="submitSpinner" class="loading-spinner" style="display: none;"></div>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Criminal Record Created Successfully</h3>
                <p class="text-sm text-gray-600 mb-6">Record ID: <span id="newRecordId" class="font-mono font-medium">#CR-2024-001</span></p>
                
                <div class="flex flex-col gap-3">
                    <button onclick="createCaseFile()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Create New Case File
                    </button>
                    <button onclick="linkToExistingCase()" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Link to Existing Case
                    </button>
                    <button onclick="viewRecord()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        View Record Details
                    </button>
                    <button onclick="addAnotherRecord()" class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        Add Another Record
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form state management
        let formData = {};
        let uploadedPhoto = null;
        let duplicateCheckPerformed = false;

        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            updateProgress();
            generateCSRFToken();
        });

        function setupEventListeners() {
            // Form field listeners for progress tracking
            const formFields = document.querySelectorAll('input, select, textarea');
            formFields.forEach(field => {
                field.addEventListener('input', updateProgress);
                field.addEventListener('change', updateProgress);
            });

            // Photo upload listeners
            const dropZone = document.getElementById('photoDropZone');
            const photoInput = document.getElementById('photoInput');

            dropZone.addEventListener('click', () => photoInput.click());
            dropZone.addEventListener('dragover', handleDragOver);
            dropZone.addEventListener('dragleave', handleDragLeave);
            dropZone.addEventListener('drop', handleDrop);
            photoInput.addEventListener('change', handleFileSelect);

            // Remove photo listener
            document.getElementById('removePhoto').addEventListener('click', removePhoto);

            // Duplicate check listener
            document.getElementById('checkDuplicates').addEventListener('click', checkForDuplicates);

            // Form submission
            document.getElementById('criminalRecordForm').addEventListener('submit', handleFormSubmit);

            // Auto-check duplicates when name and DOB are filled
            document.getElementById('firstName').addEventListener('blur', autoCheckDuplicates);
            document.getElementById('lastName').addEventListener('blur', autoCheckDuplicates);
            document.getElementById('dateOfBirth').addEventListener('change', autoCheckDuplicates);
        }

        function updateProgress() {
            const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
            let filledFields = 0;

            requiredFields.forEach(field => {
                if (field.value.trim() !== '') {
                    filledFields++;
                }
            });

            const progress = Math.round((filledFields / requiredFields.length) * 100);
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').textContent = progress + '% Complete';
        }

        // Photo upload handling
        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('dragover');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('dragover');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        }

        function handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                handleFile(file);
            }
        }

        function handleFile(file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showNotification('Please select a valid image file (JPG, PNG, or GIF)', 'error');
                return;
            }

            // Validate file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                showNotification('File size must be less than 5MB', 'error');
                return;
            }

            // Show upload progress
            document.getElementById('uploadProgress').style.display = 'block';
            
            // Simulate upload progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 30;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    setTimeout(() => {
                        document.getElementById('uploadProgress').style.display = 'none';
                        showPhotoPreview(file);
                    }, 500);
                }
                document.getElementById('uploadBar').style.width = progress + '%';
                document.getElementById('uploadPercent').textContent = Math.round(progress) + '%';
            }, 200);

            uploadedPhoto = file;
        }

        function showPhotoPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('photoPreview').style.display = 'block';
                document.getElementById('photoDropZone').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        function removePhoto() {
            uploadedPhoto = null;
            document.getElementById('photoPreview').style.display = 'none';
            document.getElementById('photoDropZone').style.display = 'block';
            document.getElementById('photoInput').value = '';
        }

        // Duplicate checking
        function autoCheckDuplicates() {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const dateOfBirth = document.getElementById('dateOfBirth').value;

            if (firstName && lastName && dateOfBirth) {
                setTimeout(checkForDuplicates, 1000); // Debounce
            }
        }

        function checkForDuplicates() {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const dateOfBirth = document.getElementById('dateOfBirth').value;

            if (!firstName || !lastName || !dateOfBirth) {
                showNotification('Please fill in name and date of birth to check for duplicates', 'error');
                return;
            }

            // Simulate API call to check duplicates
            showNotification('Checking for duplicate records...', 'info');
            
            setTimeout(() => {
                // Simulate finding potential duplicates
                const duplicates = [
                    { id: 'CR-2023-045', name: 'John Smith', dob: '1985-03-15', similarity: '95%' },
                    { id: 'CR-2022-128', name: 'Jon Smith', dob: '1985-03-15', similarity: '87%' }
                ];

                if (duplicates.length > 0) {
                    showDuplicateWarning(duplicates);
                } else {
                    showNotification('No duplicate records found', 'success');
                    document.getElementById('duplicateWarning').style.display = 'none';
                }
                
                duplicateCheckPerformed = true;
            }, 2000);
        }

        function showDuplicateWarning(duplicates) {
            const resultsDiv = document.getElementById('duplicateResults');
            resultsDiv.innerHTML = duplicates.map(dup => 
                `<div class="flex justify-between items-center py-2 border-b border-yellow-200 last:border-b-0">
                    <div>
                        <strong>${dup.id}</strong> - ${dup.name} (DOB: ${dup.dob})
                    </div>
                    <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded">${dup.similarity} match</span>
                </div>`
            ).join('');
            
            document.getElementById('duplicateWarning').style.display = 'block';
        }

        // Form submission
        function handleFormSubmit(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            submitBtn.querySelector('span').textContent = 'Creating Record...';

            // Simulate form submission
            setTimeout(() => {
                // Reset button state
                submitBtn.disabled = false;
                spinner.style.display = 'none';
                submitBtn.querySelector('span').textContent = 'Create Criminal Record';

                // Show success modal
                const recordId = 'CR-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
                document.getElementById('newRecordId').textContent = '#' + recordId;
                document.getElementById('successModal').style.display = 'flex';

                showNotification('Criminal record created successfully!', 'success');
            }, 3000);
        }

        function validateForm() {
            const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                showNotification('Please fill in all required fields', 'error');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            // Validate date of birth (not in future)
            const dob = new Date(document.getElementById('dateOfBirth').value);
            if (dob > new Date()) {
                showNotification('Date of birth cannot be in the future', 'error');
                document.getElementById('dateOfBirth').focus();
                return false;
            }

            // Validate offense date (not in future)
            const offenseDate = new Date(document.getElementById('offenseDate').value);
            if (offenseDate > new Date()) {
                showNotification('Offense date cannot be in the future', 'error');
                document.getElementById('offenseDate').focus();
                return false;
            }

            return true;
        }

        function saveDraft() {
            const formData = new FormData(document.getElementById('criminalRecordForm'));
            // Simulate saving draft
            showNotification('Draft saved successfully', 'success');
        }

        // Success modal actions
        function createCaseFile() {
            document.getElementById('successModal').style.display = 'none';
            // Redirect to case file creation
            showNotification('Redirecting to case file creation...', 'info');
        }

        function linkToExistingCase() {
            document.getElementById('successModal').style.display = 'none';
            // Show case selection modal
            showNotification('Opening case selection...', 'info');
        }

        function viewRecord() {
            document.getElementById('successModal').style.display = 'none';
            // Redirect to record view
            showNotification('Opening record details...', 'info');
        }

        function addAnotherRecord() {
            document.getElementById('successModal').style.display = 'none';
            // Reset form
            document.getElementById('criminalRecordForm').reset();
            removePhoto();
            document.getElementById('duplicateWarning').style.display = 'none';
            duplicateCheckPerformed = false;
            updateProgress();
            generateCSRFToken();
        }

        // Utility functions
        function generateCSRFToken() {
            // Generate a random CSRF token
            const token = Math.random().toString(36).substring(2) + Date.now().toString(36);
            document.getElementById('csrfToken').value = token;
        }

        function showNotification(message, type) {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(n => n.remove());

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => notification.classList.add('show'), 100);
            
            // Hide notification after 4 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        /*
        PHP Backend Code Structure:

        // config/database.php
        <?php
        $host = 'localhost';
        $dbname = 'police_records';
        $username = 'your_username';
        $password = 'your_password';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
        ?>

        // api/criminal_records.php
        <?php
        session_start();
        require_once '../config/database.php';

        header('Content-Type: application/json');

        // CSRF Protection
        function validateCSRFToken($token) {
            return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
        }

        // Generate CSRF Token
        function generateCSRFToken() {
            $token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $token;
            return $token;
        }

        $method = $_SERVER['REQUEST_METHOD'];

        switch($method) {
            case 'POST':
                if (isset($_POST['action'])) {
                    switch($_POST['action']) {
                        case 'check_duplicates':
                            checkDuplicates();
                            break;
                        case 'create_record':
                            createCriminalRecord();
                            break;
                        case 'upload_photo':
                            uploadPhoto();
                            break;
                    }
                }
                break;
            case 'GET':
                if (isset($_GET['action']) && $_GET['action'] === 'csrf_token') {
                    echo json_encode(['token' => generateCSRFToken()]);
                }
                break;
        }

        function checkDuplicates() {
            global $pdo;
            
            if (!validateCSRFToken($_POST['csrf_token'])) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                return;
            }
            
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
            $dateOfBirth = $_POST['date_of_birth'];
            
            // Check for exact matches
            $exactQuery = "SELECT record_id, first_name, last_name, date_of_birth, 
                          CONCAT(first_name, ' ', last_name) as full_name
                          FROM criminal_records 
                          WHERE first_name = ? AND last_name = ? AND date_of_birth = ?";
            
            $exactStmt = $pdo->prepare($exactQuery);
            $exactStmt->execute([$firstName, $lastName, $dateOfBirth]);
            $exactMatches = $exactStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Check for similar matches using SOUNDEX and Levenshtein distance
            $similarQuery = "SELECT record_id, first_name, last_name, date_of_birth,
                            CONCAT(first_name, ' ', last_name) as full_name
                            FROM criminal_records 
                            WHERE (SOUNDEX(first_name) = SOUNDEX(?) OR SOUNDEX(last_name) = SOUNDEX(?))
                            AND ABS(DATEDIFF(date_of_birth, ?)) <= 365
                            AND NOT (first_name = ? AND last_name = ? AND date_of_birth = ?)";
            
            $similarStmt = $pdo->prepare($similarQuery);
            $similarStmt->execute([$firstName, $lastName, $dateOfBirth, $firstName, $lastName, $dateOfBirth]);
            $similarMatches = $similarStmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'exact_matches' => $exactMatches,
                'similar_matches' => $similarMatches
            ]);
        }

        function uploadPhoto() {
            if (!validateCSRFToken($_POST['csrf_token'])) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                return;
            }
            
            if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['error' => 'No file uploaded or upload error']);
                return;
            }
            
            $file = $_FILES['photo'];
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
                return;
            }
            
            // Validate file size (5MB limit)
            if ($file['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                echo json_encode(['error' => 'File size exceeds 5MB limit']);
                return;
            }
            
            // Generate secure filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'photo_' . uniqid() . '_' . time() . '.' . $extension;
            $uploadDir = '../uploads/photos/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                echo json_encode([
                    'success' => true,
                    'filename' => $filename,
                    'path' => $uploadPath
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save uploaded file']);
            }
        }

        function createCriminalRecord() {
            global $pdo;
            
            if (!validateCSRFToken($_POST['csrf_token'])) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                return;
            }
            
            // Validate required fields
            $requiredFields = ['first_name', 'last_name', 'date_of_birth', 'gender', 'street_address', 
                              'city', 'state', 'country', 'height', 'weight', 'eye_color', 'hair_color',
                              'offense_type', 'offense_date', 'offense_description'];
            
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => "Field '$field' is required"]);
                    return;
                }
            }
            
            try {
                $pdo->beginTransaction();
                
                // Generate record ID
                $recordId = 'CR-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                
                // Insert criminal record
                $insertQuery = "INSERT INTO criminal_records (
                    record_id, first_name, middle_name, last_name, date_of_birth, gender, nationality,
                    street_address, city, state, zip_code, country, height, weight, eye_color, hair_color,
                    identifying_marks, photo_filename, offense_type, offense_date, offense_description,
                    arresting_officer, created_by_user_id, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $pdo->prepare($insertQuery);
                $stmt->execute([
                    $recordId,
                    $_POST['first_name'],
                    $_POST['middle_name'] ?? null,
                    $_POST['last_name'],
                    $_POST['date_of_birth'],
                    $_POST['gender'],
                    $_POST['nationality'] ?? null,
                    $_POST['street_address'],
                    $_POST['city'],
                    $_POST['state'],
                    $_POST['zip_code'] ?? null,
                    $_POST['country'],
                    $_POST['height'],
                    $_POST['weight'],
                    $_POST['eye_color'],
                    $_POST['hair_color'],
                    $_POST['identifying_marks'] ?? null,
                    $_POST['photo_filename'] ?? null,
                    $_POST['offense_type'],
                    $_POST['offense_date'],
                    $_POST['offense_description'],
                    $_POST['arresting_officer'] ?? null,
                    $_SESSION['user_id'], // Current police officer's ID
                ]);
                
                // Log the creation
                $logQuery = "INSERT INTO audit_log (user_id, action, table_name, record_id, details, created_at) 
                            VALUES (?, 'CREATE', 'criminal_records', ?, ?, NOW())";
                $logStmt = $pdo->prepare($logQuery);
                $logStmt->execute([
                    $_SESSION['user_id'],
                    $recordId,
                    json_encode(['action' => 'Criminal record created', 'officer_badge' => $_SESSION['badge_number']])
                ]);
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'record_id' => $recordId,
                    'message' => 'Criminal record created successfully'
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Criminal record creation error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create criminal record']);
            }
        }
        ?>

        // Database Schema (MySQL)
        CREATE TABLE criminal_records (
            id INT PRIMARY KEY AUTO_INCREMENT,
            record_id VARCHAR(20) UNIQUE NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            middle_name VARCHAR(50),
            last_name VARCHAR(50) NOT NULL,
            date_of_birth DATE NOT NULL,
            gender ENUM('Male', 'Female', 'Other') NOT NULL,
            nationality VARCHAR(50),
            street_address VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            state VARCHAR(100) NOT NULL,
            zip_code VARCHAR(20),
            country VARCHAR(100) NOT NULL,
            height INT NOT NULL,
            weight INT NOT NULL,
            eye_color VARCHAR(20) NOT NULL,
            hair_color VARCHAR(20) NOT NULL,
            identifying_marks TEXT,
            photo_filename VARCHAR(255),
            offense_type ENUM('Felony', 'Misdemeanor', 'Infraction', 'Violation') NOT NULL,
            offense_date DATE NOT NULL,
            offense_description TEXT NOT NULL,
            arresting_officer VARCHAR(255),
            created_by_user_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by_user_id) REFERENCES users(id)
        );

        CREATE TABLE audit_log (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            action VARCHAR(50) NOT NULL,
            table_name VARCHAR(50) NOT NULL,
            record_id VARCHAR(50),
            details JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );

        CREATE INDEX idx_criminal_records_name ON criminal_records(first_name, last_name);
        CREATE INDEX idx_criminal_records_dob ON criminal_records(date_of_birth);
        CREATE INDEX idx_criminal_records_record_id ON criminal_records(record_id);
        CREATE INDEX idx_audit_log_user_id ON audit_log(user_id);
        CREATE INDEX idx_audit_log_created_at ON audit_log(created_at);
        */
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'985b611cf2867893',t:'MTc1ODk4MDA3NS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
