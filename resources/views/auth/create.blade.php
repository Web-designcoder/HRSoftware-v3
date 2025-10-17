<x-layout :showNav="false" :bodyStyle="'background-image: url(' . asset('images/banner.webp') . '); background-size: cover; background-position: center;background-repeat: no-repeat; min-height: 100vh; width: 500px; display: flex; justify-content: center; flex-direction: column; margin: auto;'">
    
    <article id="login-form" class="rounded-[30px] border border-slate-300 bg-[#051f5cb0] p-4 shadow-sm py-10 px-16">
        <img src="/images/projecthr-logo.webp" alt="Logo" class="bg-white py-4 px-10 rounded-[50px] mb-10">
        <h1 class="mb-8 text-center text-4xl font-medium text-white">Log in</h1>
        <form action="{{ route('auth.store') }}" method="POST">
            @csrf

            <div class="mb-8">
                <label for="email" style="display: none">Email</label>
                <x-login-input name="email" placeholder="Email"/>
            </div>

            <div class="mb-8">
                <label for="password" style="display: none">Password</label>
                <x-login-input name="password" type="password" placeholder="Password"/>
            </div>

            <div class="mb-8 flex justify-between text-sm font-medium">
                <div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="remember" class="rounded-sm border border-slate-400">
                        <label for="remember" class="text-white">Remember me</label>
                    </div>
                </div>
                <div>
                    <a href="#" class="text-white hover:underline">Forgot password?</a>
                </div>
            </div>

            <button class="rounded-[50px] block mx-auto border-none px-10 py-2 text-center text-lg font-semibold shadow-sm hover:opacity-90 bg-[#1892ca] text-white transition">Log in</button>
        </form>

        
    </article>

    <!-- Test Accounts Info -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm font-semibold text-blue-800 mb-2">Test Accounts:</p>
                    <div class="text-xs text-blue-700 space-y-1">
                        <p><strong>Admin:</strong> admin@hr-software.com / password</p>
                        <p><strong>Consultant:</strong> consultant@hr-software.com / password</p>
                        <p><strong>Employer:</strong> employer@hr-software.com / password</p>
                        <p><strong>Candidate:</strong> candidate@hr-software.com / password</p>
                    </div>
                </div>
</x-layout>