<x-app-layout>
  <section class="text-gray-600 body-font overflow-hidden">
    <div class="container px-5 py-24 mx-auto">
        @foreach($notifications as $notification)
      <div class="-my-8 divide-y-2 divide-gray-100">
        <div class="py-8 flex flex-wrap md:flex-nowrap">
          <div class="md:w-64 md:mb-0 mb-6 flex-shrink-0 flex flex-col">
            <span class="font-semibold title-font text-gray-700">Purchase completed</span>
            <span class="mt-1 text-gray-500 text-sm">{{ $notification->data['date'] }}</span>
          </div>
          <div class="md:flex-grow" style="margin-left: 20px";>
            <h2 class="text-2xl font-medium text-gray-900 title-font mb-2">{{ $notification->data['content']}}</h2>
            <p class="leading-relaxed"><br></p>
            {{-- <a class="text-indigo-500 inline-flex items-center mt-4">Learn More
              <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="M12 5l7 7-7 7"></path>
              </svg>
            </a> --}}
          {{-- @if ($notification->read_at !== null)
           <a class="text-indigo-500 inline-flex items-center mt-4" disabled>
            Confirmed
            <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14"></path>
              <path d="M12 5l7 7-7 7"></path>
            </svg>
           </a>
          @else
            <a href="/read/{{$notification->id}}" class="text-indigo-500 inline-flex items-center mt-4">
              Unread
               <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="M12 5l7 7-7 7"></path>
               </svg>
            </a>
          @endif --}}

          {{-- @if ($notification->read_at !== null)
           <button class="text-white bg-green-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg" style="padding-left: 32px; padding-right: 32px;" disabled>
           Confirmed
           </button>
          @else
          <a href="{{ url('read', $notification->id) }}" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg" style="padding-left: 32px; padding-right: 32px;">
          Unread
          </a>
          @endif --}}
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>
</x-app-layout>