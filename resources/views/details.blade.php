<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Movie Review | Single</title>

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
                    <a href="{{ '/' }}" id="branding">
                        <img src="{{ asset ('/images/logo.png') }}" alt="" class="logo">
                        <div class="logo-copy">
                            <h1 class="site-title">ubi W</h1>
                            <small class="site-description">Mengubah Tantangan menjadi Peluang</small>
                        </div>
                    </a> <!-- #branding -->

                    <div class="main-navigation">
                        <button type="button" class="menu-toggle"><i class="fa fa-bars"></i></button>
                        <ul class="menu">
                            <li class="menu-item"><a href="{{ '/' }}">Home</a></li>
                            <li class="menu-item"><a href="#">About</a></li>
                            <li class="menu-item current-menu-item"><a href="review.html">Movie reviews</a></li>
                            <li class="menu-item"><a href="#">Join us</a></li>
                            <li class="menu-item"><a href="#">Contact</a></li>
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
                        <div class="breadcrumbs">
                            <a href="{{ '/' }}">Home</a>
                            <span>{{ $movieObject->name }}</span>
                        </div>
                        <div class="content">
                            <div class="row">
                                <div class="col-md-6">
                                    <figure class="movie-poster"><img src="{{ asset ($movieObject->image) }}" alt="#" class="poster"></figure>
                                </div>
                                <div class="col-md-6">
                                    <h2 class="movie-title">{{ $movieObject->name }}</h2>
                                    <div class="movie-summary">
                                        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. </p>

                                        <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit sed.</p>
                                    </div>
                                    <ul class="movie-meta">
                                        <li><strong>Rating:</strong>
                                            <?php $width = ($avgRate/5 * 100); ?>
                                            <div class="star-rating" title="Rated {{ $avgRate }} out of 5"><span style="width: {{ $width }}%"><strong class="rating">{{ $avgRate }}</strong> out of 5</span></div>
                                        </li>
                                        <li><strong>Genre:</strong>
                                            <?php $len = count($genreObject); $cnt = 0 ?>
                                            @foreach ($genreObject as $genre) 
                                                {{ $genre->name }}@if($cnt != $len-1), @endif
                                                <?php $cnt++ ?>
                                            @endforeach
                                        
                                        </li>
                                        <li><strong>Production Company:</strong> {{ $movieObject->production_company }}</li>
                                    </ul>
                                </div>
                            </div> <!-- .row -->
                            <div class="entry-content">
                                <p style="text-align: justify;">{{ $movieObject->description }} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <h1 style="margin-left: 1%;color: black;font-style: italic;">Other User also Rate (Item Based Collaborative Filtering)</h1>
                        <br>
                        <div class="slider">
                            <ul class="slides">
                                @for ($i = 0 ; $i< $recommendedMovieArray['cnt'] ; $i++)
                                    <li>
                                        <div class="col-md-9 Fcol-md-9">
                                            @for($j = $i; $j<(min($i+count($recommendedMovieArray)-1,$i + 4)); $j++)
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
                    <div class="row">
                        <h1 style="margin-left: 1%;color: black;font-style: italic;">Similar Movies (Content Based on Genre)</h1>
                        <br>
                        <div class="slider">
                            <ul class="slides">
                                @for ($i = 0 ; $i< $contentBasedArray['cnt'] ; $i++)
                                    <li>
                                        <div class="col-md-9 Fcol-md-9">
                                            @for($j = $i; $j<(min($i+count($contentBasedArray)-1,$i + 4)); $j++)
                                                <div class="col-md-4 Fcol-md-4" >
                                                    <div class="latest-movie">
                                                        <div class="zoom">
                                                            <?php $url = '/details/' . $contentBasedArray[$j]['id']; ?>
                                                            <a href="{{ url ($url) }}">
                                                                <img src="{{ asset ($contentBasedArray[$j]['image']) }}" alt="Movie 3">
                                                                <div class="middle">
                                                                    <p class="imageText">{{ $contentBasedArray[$j]['name'] }}</p>
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
                                    <li>Lorem ipsum dolor</li>
                                    <li>Sit amet consecture</li>
                                    <li>Dolorem respequem</li>
                                    <li>Invenore veritae</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Help Center</h3>
                                <ul class="no-bullet">
                                    <li>Lorem ipsum dolor</li>
                                    <li>Sit amet consecture</li>
                                    <li>Dolorem respequem</li>
                                    <li>Invenore veritae</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Join Us</h3>
                                <ul class="no-bullet">
                                    <li>Lorem ipsum dolor</li>
                                    <li>Sit amet consecture</li>
                                    <li>Dolorem respequem</li>
                                    <li>Invenore veritae</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h3 class="widget-title">Social Media</h3>
                                <ul class="no-bullet">
                                    <li>Facebook</li>
                                    <li>Twitter</li>
                                    <li>Google+</li>
                                    <li>Pinterest</li>
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