<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Shopping Cart
        </h2>
    </x-slot>
    <div class="py-12">
      <form action="{{ route('purchase') }}" method="POST">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          @if ($carts->isEmpty())
          <p>カートに入れている商品がありません。</p>
          @else
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              @foreach($carts as $cart)
              @foreach($cart->products as $product)
              
                @csrf
                <input type="hidden" name="product_id[]" value="{{ $product->id }}">
                <!-- 商品情報の表示部分 -->
              
              <section class="text-gray-600 body-font overflow-hidden">
                <div class="container px-3 py-18 mx-auto">
                  <div class="lg:w-4/5 mx-auto flex flex-wrap">
                    <img alt="ecommerce" class="lg:w-1/2 w-5 lg:h-auto h-10 object-cover object-center rounded" src="{{ asset('storage/images/'.$product->image) }}">
                    <div class="lg:w-1/2 w-full lg:pl-10 lg:py-6 mt-6 lg:mt-0">
                      <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ $product->seller->name }}</h2>
                      <h1 class="text-gray-900 text-3xl title-font font-medium mb-1">{{ $product->name }}</h1>
                       {{-- <div class="flex mb-4">
                        <span class="flex items-center">
                          <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                          </svg>
                          <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                          </svg>
                          <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                          </svg>
                          <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                          </svg>
                          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                          </svg> --}}
                          <span class="text-gray-600 ml-3">{{ date('H:i', strtotime($product->start_time)) }}~{{ date('H:i', strtotime($product->end_time)) }}</span> 
                          <span class="text-gray-600 ml-3">{{ $product->quantity }} left</span>
                          
                        {{-- </span>
                        <span class="flex ml-3 pl-3 py-2 border-l-2 border-gray-200 space-x-2s">
                          <a class="text-gray-500">
                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                              <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                            </svg>
                          </a>
                          <a class="text-gray-500">
                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                              <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                            </svg>
                          </a>
                          <a class="text-gray-500">
                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                              <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                            </svg>
                          </a>
                        </span>
                      </div> --}} 
                      {{-- <p class="leading-relaxed">Fam locavore kickstarter distillery. Mixtape chillwave tumeric sriracha taximy chia microdosing tilde DIY. XOXO fam indxgo juiceramps cornhole raw denim forage brooklyn. Everyday carry +1 seitan poutine tumeric. Gastropub blue bottle austin listicle pour-over, neutra jean shorts keytar banjo tattooed umami cardigan.</p> --}}
                      <div class="flex mt-6 items-center pb-5 border-b-2 border-gray-100 mb-5">
                        {{-- <div class="flex">
                          <span class="mr-3">Color</span>
                          <button class="border-2 border-gray-300 rounded-full w-6 h-6 focus:outline-none"></button>
                          <button class="border-2 border-gray-300 ml-1 bg-gray-700 rounded-full w-6 h-6 focus:outline-none"></button>
                          <button class="border-2 border-gray-300 ml-1 bg-indigo-500 rounded-full w-6 h-6 focus:outline-none"></button>
                        </div> --}}
                        <div class="flex ml-6 items-center">
                          {{--<span class="mr-3">{{ $product->pivot->quantity }}</span>--}}

                          {{-- @foreach($quantities as $quantity)
                          <span class="mr-3">{{ $quantity }}</span> --}}

                          <div class="relative">
                            {{--@foreach($quantities as $quantity)--}}
                            {{-- <select value={{ $quantity }} class="rounded border appearance-none border-gray-300 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-base pl-3 pr-10">
                              <option>1</option>
                              <option>2</option>
                              <option>3</option>
                              <option>4</option>
                              <option>5</option>
                            </select> --}}
            

                            <select name="quantity[]" class="rounded border appearance-none border-gray-300 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-base pl-3 pr-10">
                              @for ($i = 1; $i <= $product->quantity; $i++)

                                  <option value="{{ $i }}" {{ $i == $product->pivot->quantity ? 'selected' : '' }}>{{ $i }}</option>
                              @endfor
                          </select>
                          {{--@endforeach--}}
                            <span class="absolute right-0 top-0 h-full w-10 text-center text-gray-600 pointer-events-none flex items-center justify-center">
                              <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4" viewBox="0 0 24 24">
                                <path d="M6 9l6 6 6-6"></path>
                              </svg>
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="flex">
                        <span class="title-font font-medium text-2xl text-gray-900">{{ $product->price }}円</span>
                        <div class="flex ml-auto text-white bg-red-500 border-0 py-2 px-6 focus:outline-none hover:bg-red-600 rounded"><a href="/products/{{ $product->id }}">カートから削除</a></div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

                @endforeach
                @endforeach
            </div>
            <button type="submit" class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4">
              購入する
            </button>
            @endif
        </div>
      </form>
    </div>
</x-app-layout>