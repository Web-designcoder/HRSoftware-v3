<x-layout>
    <div class="max-w-4xl mx-auto mt-10 bg-white shadow-md rounded-lg p-8">
        <h1 class="text-2xl font-bold mb-6 text-[#04215c]">Create New Job</h1>

        <form action="{{ route('admin.jobs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @include('admin.jobs._form-fields')

            <div class="pt-4">
                <button type="submit"
                    class="px-4 py-2 bg-[#04215c] text-white rounded-md hover:bg-[#06318a] transition">
                    Create Job
                </button>
                <a href="{{ route('admin.jobs.index') }}" class="ml-3 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            ClassicEditor.create(document.querySelector('#assignment_overview'))
                .catch(error => console.error(error));
        });
    </script>
</x-layout>
