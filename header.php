<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#1e293b'
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .nav-link {
                @apply px-4 py-2 text-gray-700 hover:text-primary transition-colors;
            }
            .btn-primary {
                @apply bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors;
            }
            .form-control {
                @apply w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
