<?php
session_start();
include 'header.php';
?>

<!-- Navigation -->
<nav class="bg-primary shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-end">
            <div class="flex space-x-4">
                <div class="hidden md:flex items-center space-x-4">
                    <a href="index.php" class="nav-link text-white hover:text-gray-200">Home</a>
                    <a href="edit_profile.php" class="nav-link text-white hover:text-gray-200">Edit</a>
                    <a href="reservation.php" class="nav-link text-white hover:text-gray-200">Reservation</a>
                    <a href="history.php" class="nav-link text-white hover:text-gray-200">History</a>
                    <a href="login.php" class="nav-link text-white hover:text-gray-200">Logout</a>
                </div>
            </div>
            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button class="mobile-menu-button" onclick="toggleNav()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile menu -->
    <div class="hidden md:hidden" id="navbarNav">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="index.php" class="block nav-link">Home</a>
            <a href="edit_profile.php" class="block nav-link">Edit</a>
            <a href="reservation.php" class="block nav-link">Reservation</a>
            <a href="history.php" class="block nav-link">History</a>
            <a href="login.php" class="block nav-link">Logout</a>
        </div>
    </div>
</nav>


<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 slide-in-top">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Make a Reservation</h2>

        <form action="reservation.php" method="post" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">IDNO: </label>
                    <input type="lab" class="form-control" rows="1" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lab Room: </label>
                    <input type="lab" class="form-control" rows="1" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Student Name: </label>
                    <input type="lab" class="form-control" rows="1" required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date: </label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Time In: </label>
                    <select name="time_slot" class="form-control" required>
                        <option value="">Select Time Slot</option>
                        <option value="08:00 AM - 09:00 AM">08:00 AM - 09:00 AM</option>
                        <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                        <!-- Add more time slots -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Purpose: </label>
                    <textarea name="purpose" class="form-control" rows="3" required></textarea>
                </div>
                <div class="">
                    <label class="block">Remaining Sessions: </label>
                </div>
            </div>

            <button type="submit" class="w-full btn-primary">
                Submit Reservation
            </button>
        </form>
    </div>
</div>
