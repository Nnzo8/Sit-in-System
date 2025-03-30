<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/ccswb.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Lab Monitoring System</title>
    <style>
        /* Fade in animations */
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        .slide-in-left {
            animation: slideInLeft 1s ease-out;
        }
        
        .slide-in-right {
            animation: slideInRight 1s ease-out;
        }
        
        .bounce-in {
            animation: bounceIn 1s cubic-bezier(0.36, 0, 0.66, -0.56);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Page transition animations */
        .section-transition {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .section-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-white font-sans text-gray-900">
    <!-- Minimal Navigation -->
    <nav class="fixed w-full z-50 bg-white shadow-sm transition-all duration-300">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center h-16">
                <a href="#" class="flex items-center space-x-2">
                    <div class="h-8 w-8 bg-blue-500 rounded flex items-center justify-center">
                        <img src="imgs/logo.jpg" alt="Logo" class="h-6 w-6 invert">
                    </div>
                    <span class="font-medium tracking-wide">LabTrack</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-sm text-gray-600 hover:text-blue-500 transition-colors duration-200">Home</a>
                    <a href="#system" class="text-sm text-gray-600 hover:text-blue-500 transition-colors duration-200">System</a>
                    <a href="#benefits" class="text-sm text-gray-600 hover:text-blue-500 transition-colors duration-200">Benefits</a>
                    <a href="#stats" class="text-sm text-gray-600 hover:text-blue-500 transition-colors duration-200">Stats</a>
                </div>
                
                <div class="flex items-center">
                    <a href="login.php" class="text-sm px-4 py-2 text-gray-600 hover:text-blue-500 transition-colors duration-200">Sign in</a>
                    <a href="registration.php?form=register" class="text-sm px-4 py-2 rounded-md bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-200">Register</a>
                </div>
                
                <button class="md:hidden text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Clean, Minimal Hero -->
    <section id="home" class="pt-32 pb-24 bg-white section-transition">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 md:pr-16 slide-in-left">
                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-500 rounded-full text-xs font-medium mb-6">CCS MONITORING SYSTEM</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight text-gray-900 mb-6">
                        Simplified Lab<br>
                        <span class="text-blue-500">Monitoring</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 max-w-md">
                        Track computer usage, reservations, and lab availability in real-time with our modern monitoring system.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="login.php?form=register" class="px-6 py-3 bg-blue-500 text-white font-medium rounded-md hover:bg-blue-600 transition-colors duration-200">
                            Get Started
                        </a>
                        <a href="#system" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors duration-200">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 mt-12 md:mt-0 slide-in-right">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-blue-100 rounded-lg transform rotate-3"></div>
                        <img src="images/manpc.png" alt="Lab Monitoring" class="relative z-10 rounded-lg shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Geometric Divider -->
    <div class="h-40 w-full overflow-hidden bg-gray-50 relative">
        <div class="absolute -bottom-1/2 -left-1/4 w-1/2 h-full bg-blue-50 rounded-full"></div>
        <div class="absolute -top-1/2 -right-1/4 w-1/3 h-full bg-blue-100 rounded-full opacity-70"></div>
    </div>

    <!-- System Overview Minimal Cards -->
    <section id="system" class="py-20 bg-gray-50 section-transition">
        <div class="container mx-auto px-6">
            <div class="max-w-xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-500 rounded-full text-xs font-medium mb-4">SYSTEM COMPONENTS</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Intuitive Lab Management</h2>
                <p class="text-gray-600">
                    Our system streamlines computer lab operations with these core components
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- User Management -->
                <div class="bg-white p-8 rounded-lg">
                    <div class="h-12 w-12 bg-blue-50 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">User Management</h3>
                    <p class="text-gray-600 mb-6">
                        Secure student profiles with authentication and usage tracking capabilities.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Student authentication
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Usage history tracking
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Role-based access control
                        </li>
                    </ul>
                </div>
                
                <!-- Reservations -->
                <div class="bg-white p-8 rounded-lg">
                    <div class="h-12 w-12 bg-blue-50 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Reservations</h3>
                    <p class="text-gray-600 mb-6">
                        Book computers in advance with our streamlined scheduling system.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Advanced scheduling
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Conflict prevention
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Time slot management
                        </li>
                    </ul>
                </div>
                
                <!-- Usage Analytics -->
                <div class="bg-white p-8 rounded-lg">
                    <div class="h-12 w-12 bg-blue-50 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Usage Analytics</h3>
                    <p class="text-gray-600 mb-6">
                        Data-driven insights on computer and lab utilization metrics.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Utilization reporting
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Peak time analysis
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Resource optimization
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Steps with Minimal Timeline -->
    <section class="py-20 bg-white section-transition">
        <div class="container mx-auto px-6">
            <div class="max-w-xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-500 rounded-full text-xs font-medium mb-4">HOW IT WORKS</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Simple Three-Step Process</h2>
                <p class="text-gray-600">
                    Using our lab monitoring system is straightforward and efficient
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="relative">
                    <div class="absolute top-0 left-0 -ml-4 mt-2 hidden md:block">
                        <div class="h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold">1</div>
                    </div>
                    <div class="md:pl-8">
                        <span class="inline-block md:hidden h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold mb-4">1</span>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Register Account</h3>
                        <p class="text-gray-600">
                            Create your student profile with your university credentials to access the system.
                        </p>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="relative">
                    <div class="absolute top-0 left-0 -ml-4 mt-2 hidden md:block">
                        <div class="h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold">2</div>
                    </div>
                    <div class="md:pl-8">
                        <span class="inline-block md:hidden h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold mb-4">2</span>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Book a Station</h3>
                        <p class="text-gray-600">
                            Reserve a computer for your preferred time slot through the intuitive booking interface.
                        </p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="relative">
                    <div class="absolute top-0 left-0 -ml-4 mt-2 hidden md:block">
                        <div class="h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold">3</div>
                    </div>
                    <div class="md:pl-8">
                        <span class="inline-block md:hidden h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold mb-4">3</span>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Use Laboratory</h3>
                        <p class="text-gray-600">
                            Check in at your reserved time and use the computer resources for your academic needs.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Benefits with Minimal Icons -->
    <section id="benefits" class="py-20 bg-gray-50 section-transition">
        <div class="container mx-auto px-6">
            <div class="max-w-xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-500 rounded-full text-xs font-medium mb-4">BENEFITS</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Use Our System</h2>
                <p class="text-gray-600">
                    The College of Computer Studies Lab Monitoring System provides numerous advantages
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Benefit 1 -->
                <div class="bg-white p-6 rounded-lg border border-gray-100">
                    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Time Saving</h3>
                    <p class="text-gray-600 text-sm">
                        No more waiting around to see if a computer is available - book in advance.
                    </p>
                </div>
                
                <!-- Benefit 2 -->
                <div class="bg-white p-6 rounded-lg border border-gray-100">
                    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Enhanced Security</h3>
                    <p class="text-gray-600 text-sm">
                        Track who uses each computer and when, improving accountability.
                    </p>
                </div>
                
                <!-- Benefit 3 -->
                <div class="bg-white p-6 rounded-lg border border-gray-100">
                    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Resource Optimization</h3>
                    <p class="text-gray-600 text-sm">
                        Efficiently allocate computers based on real usage data and needs.
                    </p>
                </div>
                
                <!-- Benefit 4 -->
                <div class="bg-white p-6 rounded-lg border border-gray-100">
                    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Remote Access</h3>
                    <p class="text-gray-600 text-sm">
                        Book your lab sessions from anywhere, anytime using our online system.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section with Minimal Design -->
    <section id="stats" class="py-20 bg-white section-transition">
        <div class="container mx-auto px-6">
            <div class="max-w-xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-500 rounded-full text-xs font-medium mb-4">BY THE NUMBERS</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">System Statistics</h2>
                <p class="text-gray-600">
                    Data highlighting the effectiveness of our lab monitoring solution
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Stat 1 -->
                <div class="p-6 bg-blue-50 rounded-lg text-center bounce-in">
                    <p class="text-3xl lg:text-4xl font-bold text-blue-600 mb-2">50+</p>
                    <p class="text-sm text-gray-700">Computers Managed</p>
                </div>
                
                <!-- Stat 2 -->
                <div class="p-6 bg-blue-50 rounded-lg text-center bounce-in" style="animation-delay: 0.2s;">
                    <p class="text-3xl lg:text-4xl font-bold text-blue-600 mb-2">1,000+</p>
                    <p class="text-sm text-gray-700">Student Users</p>
                </div>
                
                <!-- Stat 3 -->
                <div class="p-6 bg-blue-50 rounded-lg text-center bounce-in" style="animation-delay: 0.4s;">
                    <p class="text-3xl lg:text-4xl font-bold text-blue-600 mb-2">12h</p>
                    <p class="text-sm text-gray-700">Daily Operation</p>
                </div>
                
                <!-- Stat 4 -->
                <div class="p-6 bg-blue-50 rounded-lg text-center bounce-in" style="animation-delay: 0.6s;">
                    <p class="text-3xl lg:text-4xl font-bold text-blue-600 mb-2">95%</p>
                    <p class="text-sm text-gray-700">Resource Efficiency</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple CTA -->
    <section class="py-16 bg-blue-500 section-transition">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-2xl md:text-3xl font-semibold text-white mb-6">
                Ready to optimize your lab experience?
            </h2>
            <a href="login.php" class="inline-block px-6 py-3 bg-white text-blue-600 font-medium rounded-md hover:bg-gray-100 transition-colors duration-200 shadow-md">
                Get Started Now
            </a>
        </div>
    </section>

    <!-- Minimal Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 section-transition">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="h-8 w-8 bg-blue-500 rounded flex items-center justify-center">
                            <img src="images/ccswb.png" alt="Logo" class="h-6 w-6 invert">
                        </div>
                        <span class="text-white font-medium">LabTrack</span>
                    </div>
                    <p class="text-sm">
                        A modern solution for computer laboratory tracking and management.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Documentation</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Help Center</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Terms of Service</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Cookie Policy</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Contact</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm">support@ccs-monitor.edu.ph</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm">+63 32 123 4567</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm">
                    &copy; 2025 College of Computer Studies. All rights reserved.
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Basic scroll animation for navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 10) {
                navbar.classList.add('shadow');
            } else {
                navbar.classList.remove('shadow');
            }
        });

        // Section transition animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add transition class to all sections
            document.querySelectorAll('section').forEach(section => {
                section.classList.add('section-transition');
            });

            // Intersection Observer for section animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('section-visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe all sections
            document.querySelectorAll('.section-transition').forEach(section => {
                observer.observe(section);
            });

            // Smooth scroll for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = document.querySelector(this.getAttribute('href'));
                    if (section) {
                        section.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>