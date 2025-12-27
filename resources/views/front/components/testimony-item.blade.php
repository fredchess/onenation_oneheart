<div class="item">
    <div class="testimony-wrap">
        <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-quote-left"></div>
        <div class="text">
            <p class="mb-4">{{$testimony["message"]}}</p>
            <div class="d-flex align-items-center">
                @if($testimony["image"] != null)
                <div class="user-img" style="background-image: url({{$testimony["image"]}})">
                </div>
                @endif
                <div class="ps-3">
                    <p class="name">{{$testimony["name"]}}</p>
                    <span class="position">{{$testimony["title"]}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
