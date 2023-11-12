@if($page->sections->count())
    @foreach($page->sections()->orderBy('order')->get() as $section)
        @include($section->view, ['page' => $page, 'section' => $section])
    @endforeach
@else
    <div>
        {!! $page->body !!}
    </div>
@endif
