<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Marble Demo</title>

        <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    </head>

    <body>
        <nav class="navbar navbar-inverse navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-top" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Marble {{$locale}}</a>
                </div>
                
                <div class="collapse navbar-collapse" id="nav-top">
                    <ul class="nav navbar-nav">
                        @foreach($menuItems as $menuItem)
                            <li class="{{ $node->id == $menuItem->attributes->node->value[$locale] ? "active" : "" }}"><a href="{{url("/$locale/{$menuItem->attributes->slug->value[$locale]}")}}">{{$menuItem->attributes->name->value[$locale]}}</a></li>
                        @endForeach
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Select Language <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                @foreach($languages as $language)
                                    <li class="{{ $locale == $language->id ? 'active' : '' }}">
                                        @if($node->slug)
                                            <a href="{{$node->slug[$language->id]}}">{{$language->name}}</a>
                                        @else
                                            <a href="/{{$language->id}}">{{$language->name}}</a>
                                        @endIf
                                    </li>
                                @endForeach
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            @yield("content")
        </div>
        <script src="{{ URL::asset("assets/js/jquery.js") }}"></script>
        <script src="{{ URL::asset("assets/js/bootstrap.js") }}"></script>
    </body>
</html>
