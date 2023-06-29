<x-app-layout>
  <div style="background-color: #EDEBDA;">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Product List
        </h2>
    </x-slot>
  </div>
    

    <div style="background-color:#F8F7EE" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          {{-- <form action="{{ route('products.filter') }}" method="GET">
            @csrf
            <select name="category">
              <option value="">{{ __('Select Category') }}</option>
              <option value="all">All</option>
              <x-category-select />
            </select>
            <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Search</button>
            @if (session('error'))
              <div class="bg-red-500 text-white p-4 mb-4">
                  {{ session('error') }}
              </div>
            @elseif($category !== null && $category !== 'all')
              <div class="p-4 mb-4">
                Category : {{ $category }}
              </div>
            @else
            <div class="p-4 mb-4"></div>
            @endif
          </form> --}}
          <x-modal />
            <div style="background-color:#EDEBDA" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <section class="text-gray-600 body-font">
                <div class="container px-5 py-24 mx-auto">
                  {{-- @foreach($posts as $post) --}}
                  <div class="flex flex-wrap -m-4">
                  @foreach($posts as $post)
                    <div class="lg:w-1/4 md:w-1/2 p-4 w-full" @if(now() >= $post->created_at->addHour(24)) style="display: none;" @endif>
                      {{-- <a class="block relative h-48 rounded overflow-hidden"> --}}
                      <a class="block relative h-48 rounded overflow-hidden" href="{{ route('detail',$post->id) }}">
                        <img alt="ecommerce" class="object-cover object-center w-full h-full block" src="{{ asset('storage/images/'.$post->image) }}">
                      </a>
                      <div class="mt-4">
                        <h3 class="text-gray-500 text-xs tracking-widest title-font mb-1">{{ $post->seller->name }} </h3>
                        <h2 class="text-gray-900 title-font text-lg font-medium">{{ $post->name }}</h2>
                        <p class="text-gray-600 ml-1">Price : {{ $post->price }}Php</p>
                        <p class="text-gray-600 ml-1">Pick-up Time : {{ $post->available_time }}</p>
                        {{-- @if($post->cartedBy(Auth::user())->exists())
                          <a href="/products/{{ $post->id }}">カートから削除</a> --}}
                        {{-- @else --}}
                          {{-- <a href="/products/{{ $post->id }}/cart">カートに入れる</a> --}}
                        {{-- @endif --}}
                        @if ($post->quantity > 0)
                        <form action="/products/{{ $post->id }}/cart" method="POST">
                          @csrf
                          {{-- <input type="number" id="quantity" name="cart_quantity" value="{{ old('quantity') }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"> --}}
                          <div class="flex items-center">
                            <p class="ml-1">Quantity : </p>
                            <select name='cart_quantity' class="rounded border appearance-none border-gray-300 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-base pl-3 pr-10">
                              @for ($i = 1; $i <= $post->quantity; $i++)
                                    <option>{{ $i }}</option>
                              @endfor
                            </select>
                          </div>
                          <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Add to Your Cart</button>
                        </form>
                        @else
                        <p  class="@if ($post->quantity <= 0) text-red-500 @endif">SOLD OUT</p>
                        @endif
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