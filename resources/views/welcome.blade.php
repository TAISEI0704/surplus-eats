<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet"> --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link rel="icon" href="{{ asset('img/favicon.ico') }}" id="favicon">
        <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon-180x180.png">

        {{-- <script src="{{ asset('js/welcome.js') }}"></script> --}}
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollTrigger.min.js"></script>
        <script>
        gsap.registerPlugin(ScrollTrigger);
        
        </script>

        



        <title>SurPlus Eats</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
            html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
        </style>

        <style>
        body {

            font-family: 'Nunito', sans-serif;
            background-color: #F8F7EE
        }
        header {
            background-color: #EDEBDA; 
        }
        
        footer {
            background-color: #EDEBDA; 
        }
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
        }

        .area{
            overflow: hidden;

        }
        .wrap{
            /* display: flex; */
        }
        .item{
            height: 50vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .js-wrap {
            display: flex;
            justify-content: center;
        }

        .full-width-height {
          width: 100%;
          height: 800px;
          object-fit: cover;
          object-position: center;
        }





        </style>
    </head>

    <header class="text-gray-600 body-font body-font fixed-header">
        
            <div style="display: flex; justify-content: center; align-items: center;">
                <img src="img/logo.png" alt="" style="width: 100px; height: 100px;">
                <span class="ml-3 text-xl"></span>
            </div>  
          {{-- </a> --}}
        </div>
        @if (Route::has('login'))
        <div class="hidden fixed top-0 flex-col items-center justify-center right-0 px-6 pt-8 pb-4 sm:block">
            @auth
                <a href="{{ Illuminate\Support\Facades\Auth::user()->is_seller ? route('seller.dashboard') : route('dashboard') }}" class="text-sm text-gray-700 dark:text-gray-500 rounded-full border border-black" style="padding: 5px;">
                  Dashboard
                </a>
            @else
                <i class="fas fa-user"></i>
                <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-500 rounded-full border border-black" style="padding: 5px;">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="text-gray-700 dark:text-gray-500 rounded-full border border-black ml-1 mr-10" style="padding: 5px;">
                    Register
                </a>
                <i class="fas fa-utensils"></i>
                <a href="{{ route('seller.login') }}" class="text-gray-700 dark:text-gray-500 rounded-full border border-black" style="padding: 5px;">
                    Log in(seller)
                </a>
                <a href="{{ route('seller.register') }}" class="text-gray-700 dark:text-gray-500 rounded-full border border-black ml-1" style="padding: 5px;">
                    Register(seller)
                </a>
            @endauth
        </div>
        @endif
    </header>


        <body class="antialiased">
            <div class="relative flex items-top justify-center min-h-screen dark:bg-gray-900 sm:items-center sm:pt-0" style="background-color: #F8F7EE">
                <img src="img/welcome9.jpeg" style="filter: blur(3px);" alt="" class="full-width-height" >
                <div class="absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center">
                    <h1 class="text-8xl font-bold" style="color: white;">Hello! We're Surplus Eats!</h1>
                    <p class="text-white"></p>
                </div>
            </div>
        </body>

    <div style=""> {{-- about us 部分 --}}
        
        <section class="text-gray-600 body-font">
            <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-center">
              <div class="lg:flex-grow md:w-1/2 lg:pr-24 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
                <h1 class="title-font sm:text-4xl text-3xl mb-4 font-medium text-gray-900">An app to reduce food loss in Cebu and spread the joy of eating together.
                  <br class="hidden lg:inline-block">
                </h1>
                <p class="mb-8 leading-relaxed">Recently, the Philippines has been facing a serious food waste problem. 
                    <br class="lg:inline-block">As much as 9 million tons of food is wasted annually.
                    <br class="lg:inline-block">This amount is equivalent to 90 million Jollibee chicken.
                    <br class="lg:inline-block">This number translates to 800 pieces of chicken thrown away per person each year.
                    <br class="lg:inline-block">But some people need the extra food. Let's help them with this app.</p>
                <div class="flex justify-center">
                  <button class="inline-flex text-white bg-orange-300 border-0 py-2 px-6 focus:outline-none hover:bg-orange-400 rounded text-lg">Click here to request information for restaurants</button>
                </div>
              </div>
              <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6">
                <img class="object-cover object-center rounded" alt="hero" src="img/chicken.png">
              </div>
            </div>
          </section>
    </div>

    <section class="text-gray-600 body-font"> {{-- How to 部分 --}}
        <div class="container px-5 py-24 mx-auto flex flex-wrap">
          <div class="lg:w-1/2 w-full mb-10 lg:mb-0 rounded-lg overflow-hidden" style="width: 500px; height: 880px;">
            <img alt="feature" class="object-cover object-center h-full w-full" src="img/3step_howtouse.png" style="width: 100%; height: 100%;">
          </div>
            <div class="flex flex-col flex-wrap lg:py-6 mb-10 lg:w-1/2 lg:pl-12 lg:text-left text-center">
                <div class="flex flex-col mb-32 lg:items-start items-center">
                    <div class="w-12 h-12 inline-flex items-center justify-center rounded-full bg-orange-400 text-white mb-5">
                      <div style="width: 24px; height: 24px;">
                        <i class="far fa-sad-tear" style="font-size: 24px;"></i>
                      </div>
                    </div>
                    <div class="flex-grow">
                        <h2 class="text-gray-900 text-lg title-font font-medium mb-3">Hungry or Excess food</h2>
                        <p class="leading-relaxed text-base">Have you ever had this happen to you?
                        <br class="lg:inline-block">When you are hungry or have excess food and need to throw it away.</p>
                    </div>
                </div>
                <div class="flex flex-col mb-32 lg:items-start items-center">
                    <div class="w-12 h-12 inline-flex items-center justify-center rounded-full bg-orange-400 text-white mb-5">
                      <div>
                        <i class="fas fa-hands-helping" style="font-size: 24px;"></i> 
                      </div>
                    </div>
                    <div class="flex-grow">
                        <h2 class="text-gray-900 text-lg title-font font-medium mb-3">Select a food item or Post food information.</h2>
                        <p class="leading-relaxed text-base">After you buy food in the app, head to the store. 
                        <br class="lg:inline-block">Once you post your excess food,
                        <br class="lg:inline-block">customers will come to the store to pick up their food.</p>
                        
                    </div>
                </div>
                <div class="flex flex-col mb-32 lg:items-start items-center">
                    <div class="w-12 h-12 inline-flex items-center justify-center rounded-full bg-orange-400 text-white mb-5">
                        {{-- <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-6 h-6" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg> --}}
                        <div style="width: 24px; height: 24px;">
                          <i class="far fa-smile" style="font-size: 24px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <h2 class="text-gray-900 text-lg title-font font-medium mb-3">Full stomach or no extra food</h2>
                        <p class="leading-relaxed text-base">You can fill your stomach at a lower price.
                        <br class="lg:inline-block">And you can take back your surplus food at a lower price.</p>
                        
                    </div>
                </div>
          </div>
        </div>
    </section>
    

    <section class="text-gray-600 body-font">{{-- User's Voice 部分 --}}
        <div class="area js-area">
            <div class="wrap js-wrap">
                <div class=" item item04 js-item container mx-auto flex justify-center px-0 py-24 md:flex-row flex-col items-center" style="transform: translate(-300px, 0); margin: 0px 50px 0px 0px; border: 1px solid black; background-color: #f5f5f5; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                    <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6 mb-10 md:mb-0">
                        <img class="object-cover object-center rounded" alt="hero" src="img/userphoto05.jpeg">
                    </div>
                    <div class=" md:w-1/2 lg:pl-2 pr-2 md:pl-6 flex flex-col md:items-start md:text-left items-center">
                            <h1 class="title-font sm:text-2xl text-1xl mb-4 font-medium text-gray-900">We no longer worry about 
                                <br class="lg:inline-block">food disposal!
                            </h1>
                            <p class="mb-8 leading-normal">I have always had a hard time throwing away edible food.
                             <br class="lg:inline-block">I thought, "Can't I sell this food somehow?" I used to think so.
                             <br class="lg:inline-block">But this app solved that problem.
                             <br class="lg:inline-block">Because you can deliver food to people
                             <br class="lg:inline-block">who are hungry just by posting your unsold food items.
                            </p>  
                    </div>
                </div>

                <div class=" item item05 js-item container mx-auto flex px-0 py-24 md:flex-row flex-col items-center" style="transform: translate(-300px, 0); margin: 0px 50px 0px 0px; border: 1px solid black; background-color: #f5f5f5; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                    <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6 mb-10 md:mb-0">
                        <img class="object-cover object-center rounded" alt="hero" src="img/userphoto02.jpeg">
                    </div>
                    <div class=" md:w-1/2 lg:pl-2 pr-2 md:pl-6 flex flex-col md:items-start md:text-left items-center">
                            <h1 class="title-font sm:text-2xl text-1xl mb-4 font-medium text-gray-900">We had a delicious meal
                             <br class="lg:inline-block">at a low price!
                            </h1>
                            <p class="mb-8 leading-normal">At first I was worried about how cheaply I could buy food.
                                <br class="lg:inline-block">When I actually tried it, I found that 
                                <br class="lg:inline-block">I could buy some foods at less than half of the regular price, which is very helpful. 
                                <br class="lg:inline-block">Thanks to this, my children don't have to go hungry anymore.
                            </p>
                    </div>
                </div>
           
                <div class=" item item06 js-item container mx-auto flex px-0 py-24 md:flex-row flex-col items-center" style="transform: translate(-300px, 0);margin: 0px 50px 0px 0px; border: 1px solid black; background-color: #f5f5f5; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                    <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6 mb-10 md:mb-0">
                        <img class="object-cover object-center rounded" alt="hero" src="img/userphoto03.jpeg">
                    </div>
                    <div class=" md:w-1/2 lg:pl-2 pr-2 md:pl-6 flex flex-col md:items-start md:text-left items-center">
                            <h1 class="title-font sm:text-2xl text-1xl mb-4 font-medium text-gray-900">It's great to see surplus food
                                <br class="lg:inline-block">going to hungry people.
                            </h1>
                            <p class="mb-8 leading-normal">The surplus of food was a serious problem for us.
                                <br class="lg:inline-block">This is because even just throwing it away costs money, regardless. 
                                <br class="lg:inline-block">With this app, however, you can share your surplus food with hungry children.
                                <br class="lg:inline-block">I think this system is having a positive impact on the children of Cebu.
                            </p>

                    </div>
                </div>

                <div class=" item item07 js-item container mx-auto flex px-0 py-24 md:flex-row flex-col items-center" style="width: 600px; transform: translate(-300px, 0); margin: 0px 0px 0px 0px; border: 1px solid black; background-color: #f5f5f5; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                    <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6 mb-10 md:mb-0">
                        <img class="object-cover object-center rounded" alt="hero" src="img/userphoto04.jpeg">
                    </div>
                    <div class=" md:w-1/2 lg:pl-2 pr-2 md:pl-6 flex flex-col md:items-start md:text-left items-center">
                            <h1 class="title-font sm:text-2xl text-1xl mb-4 font-medium text-gray-900">The hungry children ate
                                <br class="lg:inline-block">the meal with relish.
                            </h1>
                            <p class="mb-8 leading-normal">I was a volunteer in a poor community in Cebu.
                                <br class="lg:inline-block">There, I found out that children were not getting enough food,
                                <br class="lg:inline-block">and I thought I could do something to help them. 
                                <br class="lg:inline-block">As a result, I was able to buy good food for a small cost.
                                <br class="lg:inline-block">I am very happy that my children have the opportunity to eat more rice than before. 
                            </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="text-gray-600 body-font">{{-- User満足度 --}}
        <div class="container mx-auto flex px-5 py-24 my-20 md:flex-row flex-col items-center" style="">
          <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6 mb-10 md:mb-0">
            <img class="object-cover object-center rounded" alt="hero" src="img/3D Bubble Work Breakdown Structure Pie Chart (2).png">
          </div>
          <div class="lg:flex-grow md:w-1/2 lg:pl-24 md:pl-16 flex flex-col md:items-start md:text-left items-center text-center">
            <h1 class="title-font sm:text-4xl text-3xl mb-4 font-medium text-gray-900">High user satisfaction.
              <br class="hidden lg:inline-block">
            </h1>
            <p class="mb-8 leading-relaxed">About 80% of users say they are happy with the service. Furthermore, the number of users continues to grow. Now, why don't you get started?</p>
            <div class="flex justify-center">
              <button class="inline-flex text-white bg-orange-300 border-0 py-2 px-6 focus:outline-none hover:bg-orange-400 rounded text-lg">
                Click here to request information for restaurants
            </button>
            </div>
          </div>
        </div>
      </section>

    {{-- <section class="text-gray-600 body-font">{{-- User's Voice パターン2部分 --}}
        {{-- <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <h1 class="text-2xl font-medium title-font mb-4 text-gray-900 tracking-widest">User's Voice</h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Here are some of the comments we have received from our customers.</p>
          </div>
          <div class="flex flex-wrap -m-4">
            <div class="p-4 lg:w-1/2">
              <div class="h-full flex sm:flex-row flex-col items-center sm:justify-start justify-center text-center sm:text-left">
                <img alt="team" class="flex-shrink-0 rounded-lg w-48 h-48 object-cover object-center sm:mb-0 mb-4" src="https://dummyimage.com/200x200">
                <div class="flex-grow sm:pl-8">
                  <h2 class="title-font font-medium text-lg text-gray-900">Holden Caulfield</h2>
                  <h3 class="text-gray-500 mb-3">UI Developer</h3>
                  <p class="mb-4">DIY tote bag drinking vinegar cronut adaptogen squid fanny pack vaporware.</p>
                  <span class="inline-flex">
                    <a class="text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                      </svg>
                    </a>
                  </span>
                </div>
              </div>
            </div>
            <div class="p-4 lg:w-1/2">
              <div class="h-full flex sm:flex-row flex-col items-center sm:justify-start justify-center text-center sm:text-left">
                <img alt="team" class="flex-shrink-0 rounded-lg w-48 h-48 object-cover object-center sm:mb-0 mb-4" src="https://dummyimage.com/201x201">
                <div class="flex-grow sm:pl-8">
                  <h2 class="title-font font-medium text-lg text-gray-900">Alper Kamu</h2>
                  <h3 class="text-gray-500 mb-3">Designer</h3>
                  <p class="mb-4">DIY tote bag drinking vinegar cronut adaptogen squid fanny pack vaporware.</p>
                  <span class="inline-flex">
                    <a class="text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                      </svg>
                    </a>
                  </span>
                </div>
              </div>
            </div>
            <div class="p-4 lg:w-1/2">
              <div class="h-full flex sm:flex-row flex-col items-center sm:justify-start justify-center text-center sm:text-left">
                <img alt="team" class="flex-shrink-0 rounded-lg w-48 h-48 object-cover object-center sm:mb-0 mb-4" src="https://dummyimage.com/204x204">
                <div class="flex-grow sm:pl-8">
                  <h2 class="title-font font-medium text-lg text-gray-900">Atticus Finch</h2>
                  <h3 class="text-gray-500 mb-3">UI Developer</h3>
                  <p class="mb-4">DIY tote bag drinking vinegar cronut adaptogen squid fanny pack vaporware.</p>
                  <span class="inline-flex">
                    <a class="text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                      </svg>
                    </a>
                  </span>
                </div>
              </div>
            </div>
            <div class="p-4 lg:w-1/2">
              <div class="h-full flex sm:flex-row flex-col items-center sm:justify-start justify-center text-center sm:text-left">
                <img alt="team" class="flex-shrink-0 rounded-lg w-48 h-48 object-cover object-center sm:mb-0 mb-4" src="https://dummyimage.com/206x206">
                <div class="flex-grow sm:pl-8">
                  <h2 class="title-font font-medium text-lg text-gray-900">Henry Letham</h2>
                  <h3 class="text-gray-500 mb-3">Designer</h3>
                  <p class="mb-4">DIY tote bag drinking vinegar cronut adaptogen squid fanny pack vaporware.</p>
                  <span class="inline-flex">
                    <a class="text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                      </svg>
                    </a>
                    <a class="ml-2 text-gray-500">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                      </svg>
                    </a>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section> --}}


    <footer class="text-gray-600 body-font">{{-- footer部分 --}}
      <div class="flex">
        <div class="container px-5 py-2 mx-auto flex items-center sm:flex-row flex-col">
            <img src="img/logo.png" alt="" style="height: 100px; width: 100px">
             &copy;Surplus Eats.inc 2023
        </div>
        
        <span class="inline-flex sm:ml-auto sm:mt-0 mt-4 mr-4 justify-center sm:justify-start">
            <a class="text-gray-500">
            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
            </svg>
            </a>
            <a class="ml-3 text-gray-500">
            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
            </svg>
            </a>
            <a class="ml-3 text-gray-500">
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
            </svg>
            </a>
            <a class="ml-3 text-gray-500">
            <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="0" class="w-5 h-5" viewBox="0 0 24 24">
                <path stroke="none" d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                <circle cx="4" cy="4" r="2" stroke="none"></circle>
            </svg>
            </a>
        </span>
      </div>
    </footer>
        
</html>
