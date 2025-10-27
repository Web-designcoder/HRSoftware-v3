<x-layout>
    <div class="flex flex-col items-center justify-center h-[80vh] text-center">
        <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Access Denied</h2>
        <p class="text-gray-600 mb-6">
            You don’t have permission to view this page or perform this action.
        </p>
        <a href="{{ url()->previous() }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
           Go Back
        </a>
    </div>
</x-layout>
