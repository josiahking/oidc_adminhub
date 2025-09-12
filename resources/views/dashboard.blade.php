<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-900">Admin Hub Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Subaccounts</h2>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($organizations as $organization)
                            <li class="py-4 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $organization->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $organization->slug }}</p>
                                </div>
                                <button 
                                    class="open-subaccount-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 text-sm"
                                    data-org-id="{{ $organization->id }}"
                                >
                                    Open as Subaccount
                                </button>
                            </li>
                        @empty
                            <li class="py-4 text-center text-gray-500">No organizations found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.open-subaccount-btn').forEach(button => {
                button.addEventListener('click', async () => {
                    const orgId = button.getAttribute('data-org-id');
                    button.disabled = true;
                    button.textContent = 'Opening...';
                    
                    try {
                        const response = await fetch(`/subaccount/${orgId}`, { 
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await response.json();
                        if (data.status === 'success') {
                            alert(data.message);
                            // Optionally redirect: window.location.href = data.redirect;
                        } else {
                            throw new Error('Unexpected response');
                        }
                    } catch (error) {
                        alert('Error opening subaccount: ' + error.message);
                    } finally {
                        button.disabled = false;
                        button.textContent = 'Open as Subaccount';
                    }
                });
            });
        });
    </script>
</body>
</html>