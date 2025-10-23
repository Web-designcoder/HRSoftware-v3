@props(['showNav' => true, 'bodyClass' => '', 'bodyStyle' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Job Board</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-slate-700 {{ $bodyClass }}" 
          style="{{ $bodyStyle ?: 'background: linear-gradient(to right, #5ddfe6, #014cae);' }}">

        {{-- Optional nav --}}
        @if($showNav)
            <x-navbar/>
        @endif

        <div id="wrapper" class="mx-auto my-10 max-w-[1400px] min-h-[76vh] {{ $bodyClass }}">

            {{-- Flash messages --}}
            @if(session('success'))
                <div role="alert" class="my-8 rounded-md border-l-4 border-green-300 bg-green-100 p-4 text-green-700 opacity-75">
                    <p class="font-bold">Success!</p>
                    <p>{{session('success')}}</p>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="my-8 rounded-md border-l-4 border-red-300 bg-red-100 p-4 text-red-700 opacity-75">
                    <p class="font-bold">Error!</p>
                    <p>{{session('error')}}</p>
                </div>
            @endif

            {{-- Page content --}}
            {{ $slot }}
        </div>

        <footer class="bg-white text-center p-[17px]">
            <p>Copyright © {{date('Y')}} Project HR. All rights reserved || Software by <a href="https://www.webwizards.com.au/" target="_blank">WW</a></p>
        </footer>
    </body>
</html>