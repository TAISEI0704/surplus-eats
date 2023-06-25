<x-app-layout>
    <div style="background-color: #EDEBDA">
        <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            
        </h2>
    </x-slot>
    </div>
    

    <div style="background-color:#FBFBF6" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-xl sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-24 mx-auto">
                      <div class="flex flex-col text-center w-full mb-20">
                        <h1 class="sm:text-3xl text-2xl font-medium title-font text-gray-900"> Thank you for your purchase</h1>
                        <h2 class="text-xs text-indigo-500 tracking-widest font-medium title-font mb-1">Your purchase is complete</h2>
                      </div>
                      <div class="flex flex-wrap -m-4">
                        @php

                        @endphp
                        @foreach($purchaseHistories as $purchaseHistory)
                        <div class="p-4 md:w-1/3">
                          <div class="flex rounded-lg h-full w-full bg-gray-100 p-8 flex-col">
                            <div class="flex items-center mb-3">
                              {{-- <div class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-indigo-500 text-white flex-shrink-0">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                  <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                </svg>
                              </div> --}}
                              <h2 class="text-gray-900 text-lg title-font font-medium">{{ $purchaseHistory->product->seller->name }}</h2>
                            </div>
                            <div class="flex-grow">
                              <p class="leading-relaxed text-base">Product:{{ $purchaseHistory->product->name }}</p>
                              <p class="leading-relaxed text-base">Price:{{ $purchaseHistory->product->price }}Php</p>
                              <p class="leading-relaxed text-base">Quantity:{{ $purchaseHistory->purchase_quantity }}</p>
                              <p class="leading-relaxed text-base">Purchase Date:{{ date('Y-m-d H:i', strtotime($purchaseHistory->created_at)) }}</p>
                              {{-- <a class="mt-3 text-indigo-500 inline-flex items-center">Learn More
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                                  <path d="M5 12h14M12 5l7 7-7 7"></path>
                                </svg>
                              </a> --}}
                            </div>
                          </div>
                          {{-- <button class="flex ml-auto text-white bg-green-500 border-0 py-2 px-6 focus:outline-none hover:bg-green-600 rounded"><a href="{{ route('dashboard') }}">戻る</a></button> --}}
                        </div>
                        @endforeach
                        {{-- <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"><a href="{{ route('dashboard') }}">Back</a></button> --}}
                        {{-- <button class="flex ml-auto text-white bg-green-500 border-0 py-2 px-6 focus:outline-none hover:bg-green-600 rounded"><a href="{{ route('dashboard') }}">Back</a></button> --}}
                        {{-- <div class="p-4 md:w-1/3">
                          <div class="flex rounded-lg h-full bg-gray-100 p-8 flex-col">
                            <div class="flex items-center mb-3">
                              <div class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-indigo-500 text-white flex-shrink-0">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                                  <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                              </div>
                              <h2 class="text-gray-900 text-lg title-font font-medium">The Catalyzer</h2>
                            </div>
                            <div class="flex-grow">
                              <p class="leading-relaxed text-base">Blue bottle crucifix vinyl post-ironic four dollar toast vegan taxidermy. Gastropub indxgo juice poutine.</p>
                              <a class="mt-3 text-indigo-500 inline-flex items-center">Learn More
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                                  <path d="M5 12h14M12 5l7 7-7 7"></path>
                                </svg>
                              </a>
                            </div>
                          </div>
                        </div> --}}
                        {{-- <div class="p-4 md:w-1/3">
                          <div class="flex rounded-lg h-full bg-gray-100 p-8 flex-col">
                            <div class="flex items-center mb-3">
                              <div class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-indigo-500 text-white flex-shrink-0">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                  <circle cx="6" cy="6" r="3"></circle>
                                  <circle cx="6" cy="18" r="3"></circle>
                                  <path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12"></path>
                                </svg>
                              </div>
                              <h2 class="text-gray-900 text-lg title-font font-medium">Neptune</h2>
                            </div>
                            <div class="flex-grow">
                              <p class="leading-relaxed text-base">Blue bottle crucifix vinyl post-ironic four dollar toast vegan taxidermy. Gastropub indxgo juice poutine.</p>
                              <a class="mt-3 text-indigo-500 inline-flex items-center">Learn More
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                                  <path d="M5 12h14M12 5l7 7-7 7"></path>
                                </svg>
                              </a>
                            </div>
                          </div>
                        </div> --}}
                      </div>
                      <div>
                        @php
                            $totalPrice = 0;
                            foreach ($purchaseHistories as $purchaseHistory) {
                                $totalPrice += $purchaseHistory->product->price*$purchaseHistory->purchase_quantity;
                            }
                        @endphp
                    
                        <p>Total Price: {{ $totalPrice }}</p>
                    </div>
                    <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"><a href="{{ route('dashboard') }}">Back</a></button>
                    </div>
                    {{-- <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"><a href="{{ route('dashboard') }}">Back</a></button> --}}
                </section>
            </div>
        </div>
    </div>
</x-app-layout>