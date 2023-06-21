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
              <div style="background-color:#EDEBDA" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <section class="text-gray-600 body-font">
                  <div class="container px-5 py-24 mx-auto">
                    <div class="flex flex-wrap -m-4">
                    @foreach($posts as $post)
                      <div class="lg:w-1/4 md:w-1/2 p-4 w-full"  @if($post->quantity <= 0 && now() >= $post->updated_at->addMinutes(5)) style="display: none;" @endif>
                        <a href="{{ route('post.edit',$post->id) }}" class="block relative h-48 rounded overflow-hidden">
                          <img alt="ecommerce" class="object-cover object-center w-full h-full block" src="{{ asset('storage/images/'.$post->image) }}">
                        </a>
                        <div class="mt-4">
                          <h3 class="text-gray-500 text-xs tracking-widest title-font mb-1">{{ $post->seller->name }}</h3>
                          <h2 class="text-gray-900 title-font text-lg font-medium">{{ $post->name }}</h2>
                          <p class="mt-1">{{ $post->price }}Php</p>
                        </div>
                        {{-- <div class="lg:w-1/4 md:w-1/2 p-4 w-full flex">
                          <div class="w-1/2">
                            <a class="block relative h-48 rounded overflow-hidden">
                              <img alt="ecommerce" class="object-cover object-center w-full h-full block" src="{{ asset('storage/images/'.$post->image) }}">
                            </a>
                          </div>
                          <div class="w-1/2">
                            <div class="mt-4">
                              <h3 class="text-gray-500 text-xs tracking-widest title-font mb-1">レストラン名（仮） </h3>
                              <h2 class="text-gray-900 title-font text-lg font-medium">{{ $post->name }}</h2>
                              <p class="mt-1">{{ $post->price }}円</p>
                            </div>
                          </div>
                        </div> --}}
                      </div> 
                    @endforeach
                    </div>
                  </div>
                </section>
              </div>
          </div>
      </div>
  </x-app-layout>