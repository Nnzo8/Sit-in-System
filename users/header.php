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
                        primary: '#000080',
                        secondary: '#1e293b'
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .nav-link {
                @apply px-4 py-2 text-white hover:text-white transition-colors;
            }
            .btn-primary {
                @apply bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors;
            }
            .form-control {
                @apply w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary;
            }
            .slide-in-top {
                opacity: 0;
                transform: translateY(-50px);
                animation: slideIn 0.6s ease-out forwards;
            }

            @keyframes slideIn {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animation-delay-100 {
                animation-delay: 100ms;
            }
            .animation-delay-200 {
                animation-delay: 200ms;
            }
            .animation-delay-300 {
                animation-delay: 300ms;
            }
            .animation-delay-400 {
                animation-delay: 400ms;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
