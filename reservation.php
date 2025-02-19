<?php
session_start();
include 'header.php';
?>

<!-- Navigation -->
<nav class="bg-white shadow-lg">
    <!-- ...existing navigation code... -->
</nav>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Make a Reservation</h2>

        <form action="reservation.php" method="post" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lab Room</label>
                    <select name="lab_room" class="form-control" required>
                        <option value="">Select Lab Room</option>
                        <option value="Lab 1">Lab 1</option>
                        <option value="Lab 2">Lab 2</option>
                        <option value="Lab 3">Lab 3</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Time Slot</label>
                    <select name="time_slot" class="form-control" required>
                        <option value="">Select Time Slot</option>
                        <option value="08:00 AM - 09:00 AM">08:00 AM - 09:00 AM</option>
                        <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                        <!-- Add more time slots -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Purpose</label>
                    <textarea name="purpose" class="form-control" rows="3" required></textarea>
                </div>
            </div>

            <button type="submit" class="w-full btn-primary">
                Submit Reservation
            </button>
        </form>
    </div>
</div>
