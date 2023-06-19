<x-app-layout>
  <div style="background-color: #EDEBDA;">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catalog page
        </h2>
    </x-slot>
  </div>
    

    <div style="background-color:#F8F7EE" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="background-color:#EDEBDA" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <section class="text-gray-600 body-font">
                <div class="container px-5 py-24 mx-auto">
                  {{-- @foreach($posts as $post) --}}
                  <div class="flex flex-wrap -m-4">
                  @foreach($posts as $post)
                    <div class="lg:w-1/4 md:w-1/2 p-4 w-full">
                      {{-- <a class="block relative h-48 rounded overflow-hidden"> --}}
                      <a class="block relative h-48 rounded overflow-hidden" href="{{ route('detail',$post->id) }}">
                        <img alt="ecommerce" class="object-cover object-center w-full h-full block" src="{{ asset('storage/images/'.$post->image) }}">
                      </a>
                      <div class="mt-4">
                        <h3 class="text-gray-500 text-xs tracking-widest title-font mb-1">レストラン名（仮） </h3>
                        <h2 class="text-gray-900 title-font text-lg font-medium">{{ $post->name }}</h2>
                        <p class="mt-1">{{ $post->price }}円</p>
                        {{-- @if($post->cartedBy(Auth::user())->exists())
                          <a href="/products/{{ $post->id }}">カートから削除</a> --}}
                        {{-- @else --}}
                          {{-- <a href="/products/{{ $post->id }}/cart">カートに入れる</a> --}}
                        {{-- @endif --}}
                        <form action="/products/{{ $post->id }}/cart" method="POST">
                          @csrf
                          <input type="number" id="quantity" name="cart_quantity" value="{{ old('quantity') }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                          <p class="ml-1">個</p>
                          <button type="submit">カートに入れる</button>
                        </form>
                      </div>
                    </div>
                  @endforeach
                  </div>
                </div>
              </section>
            </div>
        </div>
    </div>
</x-app-layout>