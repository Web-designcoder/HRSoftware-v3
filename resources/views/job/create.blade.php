<x-layout>
    <div class="max-w-4xl mx-auto mt-10 bg-white shadow-md rounded-lg p-8">
        <h1 class="text-2xl font-bold mb-6">Create Job</h1>

        <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Job Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Location -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Country</label>
                    <input type="text" name="country" value="{{ old('country') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
            </div>

            <!-- Salary -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Salary</label>
                <input type="number" name="salary" value="{{ old('salary') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <!-- Experience -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Experience Level</label>
                <select name="experience"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Experience</option>
                    @foreach (\App\Models\Job::$experience as $level)
                        <option value="{{ $level }}" {{ old('experience') == $level ? 'selected' : '' }}>
                            {{ ucfirst($level) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Category</option>
                    @foreach (\App\Models\Job::$category as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Posted -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Date Posted</label>
                <input type="date" name="date_posted" value="{{ old('date_posted', now()->format('Y-m-d')) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <!-- Managed By -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Managed By</label>
                <input type="text" name="managed_by" value="{{ old('managed_by') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <!-- Assignment Overview (WYSIWYG) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assignment Overview</label>
                <textarea name="assignment_overview" id="assignment_overview" rows="10"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('assignment_overview') }}</textarea>
            </div>

            <!-- Company Logo -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Company Logo</label>
                <input type="file" name="company_logo"
                    class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none" />
            </div>

            <!-- Campaign Documents -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Campaign Documents</label>
                <input type="file" name="campaign_documents"
                    class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none" />
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create Job</button>
                <a href="{{ route('jobs.index') }}" class="ml-3 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>

    {{-- CKEditor 5 (no key, no account needed) --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor
            .create(document.querySelector('#assignment_overview'), {
                toolbar: [
                    'undo','redo','|',
                    'heading','|',
                    'bold','italic','underline','|',
                    'bulletedList','numberedList','|',
                    'link','insertTable','|',
                    'alignment','blockQuote'
                ]
            })
            .catch(error => console.error(error));
    });
    </script>

</x-layout>
