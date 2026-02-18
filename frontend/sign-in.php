<?php
// Include session configuration
require_once 'includes/session_config.php';

// If user is already authenticated, redirect to dashboard
if (isAuthenticated()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html dir="ltr">
    <head>
        <!-- Required meta tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Links Of CSS File -->
        <link rel="stylesheet" href="assets/css/remixicon.css">
        <link rel="stylesheet" href="assets/css/apexcharts.css">
        <link rel="stylesheet" href="assets/css/simplebar.css">
        <link rel="stylesheet" href="assets/css/prism.css">
        <link rel="stylesheet" href="assets/css/jsvectormap.min.css">
        <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
        <link rel="stylesheet" href="assets/css/quill.snow.css">
        <link rel="stylesheet" href="assets/css/style.css">

        <!-- Favicon -->
		<link rel="icon" type="image/png" href="assets/images/favicon.ico">

        <!-- Title -->
        <title>Khawaja Traders - Sign In</title>

        <!-- Font Family -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        <!-- Material Icons -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>

        <!-- Light/Dark Mode Button -->
        <button type="button" class="light-dark-toggle leading-none inline-block transition-all text-[#fe7a36] absolute top-[20px] md:top-[25px] ltr:right-[20px] rtl:left-[20px] ltr:md:right-[25px] rtl:md:left-[25px]" id="light-dark-toggle">
            <i class="material-symbols-outlined !text-[20px] md:!text-[22px]">
                light_mode
            </i>
        </button>
        <!-- End Light/Dark Mode Button -->

        <!-- Sign In -->
        <div class="bg-white dark:bg-[#0a0e19] min-h-screen flex items-center py-[20px] md:py-[40px]">
            <div class="mx-auto px-[12.5px] md:max-w-[720px] lg:max-w-[960px] xl:max-w-[1255px] w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-[25px] items-center">
                    <div class="xl:ltr:-mr-[25px] xl:rtl:-ml-[25px] 2xl:ltr:-mr-[45px] 2xl:rtl:-ml-[45px] rounded-[25px] order-2 lg:order-1">
                        <img src="assets/images/logo/signin.svg" alt="KMI Cyber Security" class="rounded-[25px] w-full h-[500px] object-contain">
                    </div>
                    <div class="xl:ltr:pl-[90px] xl:rtl:pr-[90px] 2xl:ltr:pl-[120px] 2xl:rtl:pr-[120px] order-1 lg:order-2">
                        <img src="assets/images/logo/kmi-logo.png" alt="logo" style="height: 76px; width: 300px;" class="inline-block dark:hidden">
                        <img src="assets/images/logo/kmi-logo.png" alt="logo" style="height: 76px; width: 300px;" class="hidden dark:inline-block">
                        <div class="my-[17px] md:my-[25px]">
                            <h1 class="!font-semibold !text-[22px] md:!text-xl lg:!text-2xl !mb-[5px] md:!mb-[7px]">
                                Welcome back to Khawaja Traders!
                            </h1>
                            <p class="font-medium lg:text-md text-[#445164] dark:text-gray-400">
                                Sign in to access your dashboard
                            </p>
                        </div>
                        
                        <!-- Login Form -->
                        <form id="loginForm" class="space-y-[25px]">
                            <div class="mb-[10px] md:mb-[10px] last:mb-0 relative float-group">
                                <input type="email" id="email" name="email" required class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[17px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500" placeholder="">
                                <label for="email" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Email Address</label>
                            </div>
                            <div class="mb-[10px] md:mb-[10px] last:mb-0 relative float-group" id="passwordHideShow">
                                <input type="password" id="password" name="password" required class="h-[40px] rounded-md text-black dark:text-white border border-gray-200 dark:border-[#172036] bg-white dark:bg-[#0c1427] px-[17px] block w-full outline-0 transition-all placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-primary-500" placeholder="">
                                <label for="password" class="float-label bg-white dark:bg-[#0c1427] text-gray-500 dark:text-gray-400">Password</label>
                                <button class="absolute text-lg ltr:right-[15px] rtl:left-[15px] top-1/2 -translate-y-1/2 transition-all hover:text-primary-500 z-10 cursor-pointer p-1" id="toggleButton" type="button" style="pointer-events: auto; background: transparent; border: none; outline: none;">
                                    <i class="ri-eye-off-line"></i>
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <span></span>
                            </div>
                            <button type="submit" id="loginBtn" class="md:text-md block w-full text-center transition-all rounded-md font-medium mt-[20px] md:mt-[25px] py-[12px] px-[25px] text-white bg-primary-500 hover:bg-primary-400 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="flex items-center justify-center gap-[5px]">
                                    <i class="material-symbols-outlined" id="loginIcon">
                                        login
                                    </i>
                                    <span id="loginText">Sign In</span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Sign In -->
        
        <!-- Toast Notification Container -->
        <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;"></div>
        
        <!-- Links Of JS File -->
        <script src="assets/js/apexcharts.min.js"></script>
        <script src="assets/js/fslightbox.js"></script>
        <script src="assets/js/simplebar.min.js"></script>
        <script src="assets/js/prism.js"></script>
        <script src="assets/js/clipboard.min.js"></script>
        <script src="assets/js/swiper-bundle.min.js"></script>
        <script src="assets/js/fullcalendar.min.js"></script>
        <script src="assets/js/jsvectormap.min.js"></script>
        <script src="assets/js/world-merc.js"></script>
        <script src="assets/js/quill.min.js"></script>
        <script src="assets/js/custom.js"></script>

        <!-- Login Script -->
        <script>
            window.addEventListener('load', function() {
                // Add a small delay to ensure everything is loaded, including custom.js
                setTimeout(function() {
                    const loginForm = document.getElementById('loginForm');
                    const loginBtn = document.getElementById('loginBtn');
                    const loginIcon = document.getElementById('loginIcon');
                    const loginText = document.getElementById('loginText');
                    const toggleButton = document.getElementById('toggleButton');
                    const passwordInput = document.getElementById('password');

                    // Initialize password field state
                    // Force set the type to password
                    passwordInput.setAttribute('type', 'password');
                    
                    const icon = toggleButton.querySelector('i');
                    icon.className = 'ri-eye-off-line'; // Set initial icon state

                    // Remove any existing event listeners from custom.js
                    const newToggleButton = toggleButton.cloneNode(true);
                    toggleButton.parentNode.replaceChild(newToggleButton, toggleButton);
                    const finalToggleButton = document.getElementById('toggleButton');
                    
                    // Password toggle functionality
                    finalToggleButton.addEventListener('click', function() {
                        // Force check the current type and ensure it's properly set
                        let currentType = passwordInput.getAttribute('type');
                        if (!currentType) {
                            currentType = passwordInput.type;
                        }
                        
                        // Determine new type based on current state
                        const newType = (currentType === 'password') ? 'text' : 'password';
                        
                        // Force set both attribute and property
                        passwordInput.setAttribute('type', newType);
                        passwordInput.type = newType;
                        
                        const icon = finalToggleButton.querySelector('i');
                        
                        // Toggle the icon class
                        if (newType === 'password') {
                            icon.className = 'ri-eye-off-line';
                        } else {
                            icon.className = 'ri-eye-line';
                        }
                        
                        // Force focus back to input to ensure visibility change takes effect
                        passwordInput.focus();
                    });

                // ======================== ENTER KEY NAVIGATION ========================
                
                // Function to handle Enter key navigation
                function handleEnterNavigation(currentFieldId) {
                    console.log('Handling Enter navigation from:', currentFieldId);
                    
                    // Define the exact sequence - Match the actual form layout
                    const fieldSequence = [
                        'email',        // Email Address
                        'password',     // Password
                        'loginBtn'      // Sign In Button
                    ];
                    
                    // Find current field index
                    const currentIndex = fieldSequence.indexOf(currentFieldId);
                    console.log('Current field index:', currentIndex);
                    
                    if (currentIndex === -1) {
                        console.log('Field not found in sequence:', currentFieldId);
                        return;
                    }
                    
                    // Get next field
                    const nextIndex = currentIndex + 1;
                    console.log('Next field index:', nextIndex);
                    
                    if (nextIndex < fieldSequence.length) {
                        const nextFieldId = fieldSequence[nextIndex];
                        console.log('Next field ID:', nextFieldId);
                        
                        const nextField = document.getElementById(nextFieldId);
                        if (nextField) {
                            console.log('Focusing next field:', nextFieldId);
                            nextField.focus();
                            
                            // If it's the password field, select all text for easy replacement
                            if (nextFieldId === 'password') {
                                setTimeout(() => {
                                    nextField.select();
                                }, 10);
                            }
                        } else {
                            console.log('Next field not found:', nextFieldId);
                        }
                    } else {
                        console.log('Reached end of sequence, submitting form');
                        // If we're at the last field, submit the form
                        const form = document.getElementById('loginForm');
                        if (form) {
                            form.dispatchEvent(new Event('submit'));
                        }
                    }
                }
                
                // Global Enter key handler
                document.addEventListener('keydown', function(e) {
                    if (e.key !== 'Enter') return;
                    
                    const activeElement = document.activeElement;
                    if (!activeElement) return;
                    
                    const fieldId = activeElement.id;
                    console.log('Enter pressed in field:', fieldId);
                    
                    // Check if it's a form field we want to handle
                    const formFields = ['email', 'password'];
                    if (formFields.includes(fieldId)) {
                        e.preventDefault(); // Prevent default form submission
                        console.log('Preventing default and handling navigation');
                        handleEnterNavigation(fieldId);
                    }
                    // If it's the submit button, let the default behavior handle it
                });

                // Toast notification function (matching misc_entries.php design)
                function showToast(message, type = 'success') {
                    const toastContainer = document.getElementById('toast-container');
                    if (!toastContainer) {
                        return;
                    }

                    const toast = document.createElement('div');

                    let bgColor, textColor, iconBg, borderColor, icon, shadowColor;
                    switch (type) {
                        case 'success':
                            bgColor = '#f0fdf4';
                            textColor = '#166534';
                            iconBg = '#16a34a';
                            borderColor = '#bbf7d0';
                            icon = '✓';
                            shadowColor = 'rgba(22, 163, 74, 0.15)';
                            break;
                        case 'error':
                            bgColor = '#fef2f2';
                            textColor = '#991b1b';
                            iconBg = '#dc2626';
                            borderColor = '#fecaca';
                            icon = '✗';
                            shadowColor = 'rgba(220, 38, 38, 0.15)';
                            break;
                        case 'warning':
                            bgColor = '#fffbeb';
                            textColor = '#92400e';
                            iconBg = '#d97706';
                            borderColor = '#fed7aa';
                            icon = '⚠';
                            shadowColor = 'rgba(217, 119, 6, 0.15)';
                            break;
                        case 'info':
                            bgColor = '#eff6ff';
                            textColor = '#1e40af';
                            iconBg = '#3b82f6';
                            borderColor = '#bfdbfe';
                            icon = 'ℹ';
                            shadowColor = 'rgba(59, 130, 246, 0.15)';
                            break;
                        default:
                            bgColor = '#f0fdf4';
                            textColor = '#166534';
                            iconBg = '#16a34a';
                            borderColor = '#bbf7d0';
                            icon = '✓';
                            shadowColor = 'rgba(22, 163, 74, 0.15)';
                    }

                    toast.style.cssText = `background:${bgColor};color:${textColor};padding:16px 20px;border-radius:12px;box-shadow:0 10px 15px -3px ${shadowColor},0 4px 6px -2px rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;position:relative;z-index:9999;min-width:300px;max-width:400px;font-weight:500;font-size:14px;border:1px solid ${borderColor};transform:translateX(100%);transition:all .3s cubic-bezier(.4,0,.2,1);letter-spacing:.025em;`;

                    toast.innerHTML = `
                        <div style="width:20px;height:20px;border-radius:50%;background:${iconBg};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;color:white;flex-shrink:0;box-shadow:0 2px 4px rgba(0,0,0,.1)">${icon}</div>
                        <span style="flex:1;line-height:1.4">${message}</span>
                        <button style="background:none;border:none;color:${textColor};cursor:pointer;font-size:18px;opacity:.6;transition:opacity .2s;padding:0;margin-left:8px;flex-shrink:0;width:20px;height:20px;display:flex;align-items:center;justify-content:center" onclick="this.parentElement.remove()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.6'">×</button>`;

                    toastContainer.appendChild(toast);
                    setTimeout(() => {
                        toast.style.transform = 'translateX(0)';
                    }, 100);
                    setTimeout(() => {
                        if (toastContainer.contains(toast)) {
                            toast.style.transform = 'translateX(100%)';
                            setTimeout(() => {
                                if (toastContainer.contains(toast)) {
                                    toastContainer.removeChild(toast);
                                }
                            }, 300);
                        }
                    }, 5000);
                }

                // Login form submission
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Login form submitted');
                    
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    
                    console.log('Form data:', { email, password: '***' });
                    
                    // Validate inputs
                    if (!email || !password) {
                        console.log('Validation failed: missing fields');
                        showToast('Please fill in all fields', 'error');
                        return;
                    }
                    
                    // Show loading state
                    loginBtn.disabled = true;
                    loginIcon.textContent = 'hourglass_empty';
                    loginText.textContent = 'Signing In...';
                    
                    // Prepare data for API
                    const loginData = {
                        email: email, // Send email instead of username
                        password: password
                    };
                    
                    // Make API call through main gateway
                    console.log('Making API call to: ../api/auth/login');
                    console.log('Login data:', loginData);
                    
                    fetch('../api/auth/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(loginData)
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        
                        if (!response.ok) {
                            // Try to get error message from response
                            return response.json().then(errorData => {
                                console.log('Error response data:', errorData);
                                // Extract the specific error message from the API response
                                const errorMessage = errorData.error || errorData.message || `HTTP ${response.status}: ${response.statusText}`;
                                throw new Error(errorMessage);
                            }).catch(() => {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            });
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        console.log('API Response:', data);
                        
                        if (data.success) {
                            // Session is automatically handled by PHP
                            showToast('Login successful! Redirecting...', 'success');
                            
                            // Redirect to dashboard after 1 second
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 1000);
                        } else {
                            showToast(data.error || 'Login failed', 'error');
                            
                            // Reset button state
                            loginBtn.disabled = false;
                            loginIcon.textContent = 'login';
                            loginText.textContent = 'Sign In';
                        }
                    })
                    .catch(error => {
                        console.error('Login Error:', error);
                        console.error('Error details:', {
                            message: error.message,
                            stack: error.stack
                        });
                        
                        // Show user-friendly error message
                        let errorMessage = 'Login failed. Please try again.';
                        
                        // Check for specific error messages from the API
                        if (error.message.includes('Invalid username/email or password')) {
                            errorMessage = 'Invalid email or password. Please check your credentials.';
                        } else if (error.message.includes('Unauthorized') || error.message.includes('401')) {
                            errorMessage = 'Invalid email or password. Please check your credentials.';
                        } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                            errorMessage = 'Network error. Please check your connection and try again.';
                        } else if (error.message.includes('HTTP 401')) {
                            errorMessage = 'Invalid email or password. Please check your credentials.';
                        } else if (error.message && !error.message.includes('HTTP')) {
                            // Use the API error message if it's not a generic HTTP error
                            errorMessage = error.message;
                        }
                        
                        showToast(errorMessage, 'error');
                        
                        // Reset button state
                        loginBtn.disabled = false;
                        loginIcon.textContent = 'login';
                        loginText.textContent = 'Sign In';
                    });
                });
                }, 100); // End of setTimeout
            });

            // Check if user is already logged in (handled by PHP session)
            // No need to check sessionStorage as we're using PHP sessions
        </script>
    </body>
</html>