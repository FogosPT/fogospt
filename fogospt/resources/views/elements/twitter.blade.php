@isset($fire['id'])
    <a href="https://twitter.com/intent/tweet?button_hashtag=#fogosPT{{ $fire['id'] }}&ref_src=twsrc%5Etfw" class="twitter-hashtag-button" data-lang="pt" data-show-count="false">Tweet #fogosPT{{ $fire['id'] }}</a>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

    @if(is_array($feed) && count($feed) > 0)

    <div class="list-group">

      @foreach($feed as $tweet)
          <a  target="_blank" rel="noopener noreferrer" href="https://twitter.com/{{$tweet->user->screen_name}}/status/{{$tweet->id_str}}" class="list-group-item list-group-item-action flex-column align-items-start">

            <div class="d-flex w-100 justify-content-between">
               <img src="https://pbs.twimg.com/profile_images/992860752624091136/f5ClxNvF_normal.jpg" style="width: 25px; height: 25px; border-radius: 50%;"> 
              <h6 class="mb-1">{{$tweet->user->name}}</h6>
              <!--small class="text-muted">{{$tweet->created_at}}</small-->
            </div>
            <small class="mb-1">{{$tweet->text}}</small>
          </a>

      @endforeach
      </div>
    @endif

@endisset
