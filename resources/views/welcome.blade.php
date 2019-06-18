<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Movie Review</title>

        <!-- Loading third party fonts -->
        <link href="http://fonts.googleapis.com/css?family=Roboto:300,400,700|" rel="stylesheet" type="text/css">
        <link href="{{ asset ('/fonts/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

        <!-- Loading main css file -->
        <link rel="stylesheet" href="{{ asset ('/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset ('/css/mycss.css') }}">
        
        <!--[if lt IE 9]>
        <script src="js/ie-support/html5.js"></script>
        <script src="js/ie-support/respond.js"></script>
        <![endif]-->

    </head>


    <body>
            
        <div id="site-content">
            <header class="site-header">
                <div class="container">
                    <a href="index.html" id="branding">
                        <img src="{{ asset ('/images/logo.png') }}" alt="" class="logo">
                        <div class="logo-copy">
                            <h1 class="site-title">ubi W</h1>
                            <small class="site-description">Mengubah Tantangan menjadi Peluang</small>
                        </div>
                    </a> <!-- #branding -->

                    <div class="main-navigation">
                        <button type="button" class="menu-toggle"><i class="fa fa-bars"></i></button>
                        <ul class="menu">
                        <li class="menu-item current-menu-item"><a href="{{ '/' }}">Home</a></li>
                            <li class="menu-item"><a href="about.html">About</a></li>
                            <li class="menu-item"><a href="review.html">Movie reviews</a></li>
                            <li class="menu-item"><a href="joinus.html">Join us</a></li>
                            <li class="menu-item"><a href="contact.html">Contact</a></li>
                        </ul> <!-- .menu -->

                        <form action="#" class="search-form">
                            <input type="text" placeholder="Search...">
                            <button><i class="fa fa-search"></i></button>
                        </form>
                    </div> <!-- .main-navigation -->

                    <div class="mobile-navigation"></div>
                </div>
            </header>
            <main class="main-content">
                <div class="container">
                    <div class="page">
                        <div class="row">
                            <div class="col-md-9">
                                <h1 style="margin-left: 1%;color: black;font-style: italic;">Top Rated Film</h1>
                            </div>
                            <div class="col-md-3">
                                <h1 style="margin-left: 1%;color: black;font-style: italic;">Most Rated Film</h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9 content">
                                <div class="slider">
                                    <ul class="slides">
                                        @foreach ($topRatedMovie as $movie)
                                            <li>
                                                <?php $url = '/details/' . $movie->id; ?>
                                                <a href="{{ url ($url) }}">
                                                    <div class="topRated">
                                                        <img src="{{ asset($movie->image) }}" alt="Slide 1" class="slider-lan">
                                                        <div class="middle">
                                                                <p class="imageText"><strong>{{ $movie->name }}</strong>
                                                                    &nbsp;&nbsp;
                                                                    <i class="fa fa-star" style="font-size:24px;color:yellow"> <strong style="color:white;">{{ $movie->average_rate }}</strong></i>
                                                                    {{-- <p>{{ $movie->average_rate }}</p> --}}
                                                                </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-sm-6 col-md-12">
                                        <div class="latest-movie">
                                            <a href="#"><img src="dummy/thumb-1.jpg" alt="Movie 1"></a>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-12">
                                        <div class="latest-movie">
                                            <a href="#"><img src="dummy/thumb-2.jpg" alt="Movie 2"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- .row -->
                        <div class="row">
                            <h1 style="margin-left: 1%;color: black;font-style: italic;">User Based Recomendation</h1>
                            <br>
                            <div class="slider">
                                <ul class="slides">
                                    
                                    @for ($i = 0 ; $i< $recommendedMovieArray['cnt'] ; $i++)
                                        <li>
                                            <div class="col-md-9 Fcol-md-9">
                                                @for($j = $i; $j<(min($i+count($recommendedMovieArray)-1, $i+4)); $j++)
                                                    <div class="col-md-4 Fcol-md-4" >
                                                        <div class="latest-movie">
                                                            <div class="zoom">
                                                                <?php $url = '/details/' . $recommendedMovieArray[$j]['id']; ?>
                                                                <a href="{{ url ($url) }}">
                                                                    <img src="{{ asset ($recommendedMovieArray[$j]['image']) }}" alt="Movie 3">
                                                                    <div class="middle">
                                                                        <p class="imageText">{{ $recommendedMovieArray[$j]['name'] }}</p>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        </li>
                                    @endfor
                                </ul>
                            </div>
                        </div><!-- row -->
                    </div>
                </div> <!-- .container -->
            </main>
            <footer class="site-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">About Us</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quia tempore vitae mollitia nesciunt saepe cupiditate</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Recent Review</h3>
                                <ul class="no-bullet">
                                    <li><a href="#">Lorem ipsum dolor</a></li>
                                    <li><a href="#">Sit amet consecture</a></li>
                                    <li><a href="#">Dolorem respequem</a></li>
                                    <li><a href="#">Invenore veritae</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Help Center</h3>
                                <ul class="no-bullet">
                                    <li><a href="#">Lorem ipsum dolor</a></li>
                                    <li><a href="#">Sit amet consecture</a></li>
                                    <li><a href="#">Dolorem respequem</a></li>
                                    <li><a href="#">Invenore veritae</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Join Us</h3>
                                <ul class="no-bullet">
                                    <li><a href="#">Lorem ipsum dolor</a></li>
                                    <li><a href="#">Sit amet consecture</a></li>
                                    <li><a href="#">Dolorem respequem</a></li>
                                    <li><a href="#">Invenore veritae</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Social Media</h3>
                                <ul class="no-bullet">
                                    <li><a href="#">Facebook</a></li>
                                    <li><a href="#">Twitter</a></li>
                                    <li><a href="#">Google+</a></li>
                                    <li><a href="#">Pinterest</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Newsletter</h3>
                                <form action="#" class="subscribe-form">
                                    <input type="text" placeholder="Email Address">
                                </form>
                            </div>
                        </div>
                    </div> <!-- .row -->

                    <div class="colophon">Copyright 2014 Company name, Designed by Themezy. All rights reserved</div>
                </div> <!-- .container -->

            </footer>
        </div>
        <!-- Default snippet for navigation -->
        


        <script src="{{ asset ('/js/jquery-1.11.1.min.js') }}"></script>
        <script src="{{ asset ('/js/plugins.js') }}"></script>
        <script src="{{ asset ('/js/app.js') }}"></script>
        
    </body>

</html>