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
                <h2 class="sm:text-3xl text-2xl font-medium title-font text-gray-900">Your purchase is complete</h2>
              </div>
              <div class="flex flex-wrap -m-4">
                @foreach($purchaseHistories as $purchaseHistory)
                  <div class="p-4 md:w-1/3">
                    <div class="flex rounded-lg h-full w-full bg-gray-100 p-8 flex-col">
                      <div class="flex items-center mb-3">
                        <h2 class="text-gray-900 text-lg title-font font-medium">{{ $purchaseHistory->product->seller->name }}</h2>
                      </div>
                      <div class="flex-grow">
                        <p class="leading-relaxed text-base">Product:{{ $purchaseHistory->product->name }}</p>
                        <p class="leading-relaxed text-base">Price:{{ $purchaseHistory->product->price }}Php</p>
                        <p class="leading-relaxed text-base">Quantity:{{ $purchaseHistory->purchase_quantity }}</p>
                        <p class="leading-relaxed text-base">Purchase Date:{{ date('Y-m-d H:i', strtotime($purchaseHistory->created_at)) }}</p>
                      </div>
                      <div>
                        <button type="submit" class="text-white bg-orange-300 border-0 py-2 px-6 focus:outline-none hover:bg-orange-400 rounded"><a href="{{ route('detail', ['id' => $purchaseHistory->product->id]) }}">{{ __('Detail') }}</a></button>
                      </div>
                    </div>
                  </div>
                @endforeach
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
              <div>
                <button type="submit" class="text-white bg-gray-500 border-0 py-2 px-6 focus:outline-none hover:bg-gray-600 rounded"><a href="{{ route('dashboard') }}">Back</a></button>
              </div>
            </div>
          </section>
        </div>
      </div>
  </div>
</x-app-layout>