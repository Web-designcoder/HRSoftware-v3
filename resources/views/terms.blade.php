<x-layout :showNav="false" :bodyStyle="'background-image: url(' . asset('images/banner.webp') . '); background-size: cover; background-position: center;background-repeat: no-repeat; min-height: 100vh; margin-top:0; display: flex; justify-content: center; flex-direction: column'">
    
    <article id="login-form" class="text-white rounded-[30px] border border-slate-300 bg-[#051f5cb0] p-4 shadow-sm py-10 px-16">
        <img src="/images/projecthr-logo.webp" alt="Logo" class="bg-white py-4 px-10 rounded-[50px] mb-10 mt-5 mx-auto" style="max-width: 500px">
        <h1 class="mb-8 text-center text-4xl font-medium text-white">Terms & Conditions</h1>
        
        <p >Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod ut quam sit amet accumsan. Nunc luctus erat urna, eget dapibus lorem tempus eu. Aliquam efficitur, justo a pharetra ullamcorper, ipsum ante interdum justo, luctus sollicitudin quam ante eget nibh. Nullam gravida ex eu nulla condimentum, nec vestibulum lacus tincidunt. Etiam lacinia eu tortor quis aliquet. Proin tristique consequat elit, sed semper ex tempus ut. Duis et mauris libero. Ut elementum, libero id vehicula auctor, metus enim consectetur arcu, ut malesuada mauris augue dictum nisi.</p>

        <p>Aenean egestas vel dolor vel lacinia. Maecenas lacinia libero at dolor vulputate efficitur. Nulla facilisi. Nam nec fermentum ex, eu condimentum risus. In vulputate condimentum magna, hendrerit pellentesque mauris consectetur vel. Cras sollicitudin ornare eros, non tincidunt lacus dictum ut. Pellentesque posuere, risus vitae euismod ultricies, urna risus varius ligula, ac venenatis sem orci et ipsum. Donec imperdiet orci diam, at porta mi pharetra vel. Nulla sagittis, dolor eget fringilla lacinia, nunc sem consequat mauris, vitae varius mi arcu vel ipsum. Nullam feugiat, leo sit amet mollis iaculis, mauris dui auctor augue, quis consectetur mauris lectus id quam. Quisque placerat accumsan nunc at molestie. Praesent consectetur nulla a risus condimentum, sit amet hendrerit nulla vulputate. Praesent ultricies sed tellus eget lacinia.</p>

        <form action="{{ route('terms.accept') }}" method="POST" class="mt-10 ">
            @csrf
            <div class="mb-4">
                <label class="flex items-center justify-center">
                    <input type="checkbox" name="agree" value="1" class="mr-2">
                    <span> I agree to the Terms & Conditions</span>
                </label>
            </div>

            <button class="rounded-[50px] block mx-auto border-none px-10 py-2 text-center text-lg font-semibold shadow-sm hover:opacity-90 bg-[#1892ca] text-white transition">Continue to Dashboard</button>
        </form>
    </article>
</x-layout>