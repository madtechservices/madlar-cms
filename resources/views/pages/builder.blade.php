<x-tomato-admin-container label="{{__('Page Builder')}}">
    <x-slot:buttons>
        <x-tomato-admin-button type="link" href="{{route('admin.pages.index')}}">
            <x-heroicon-s-arrow-left class="w-4 h-4" />
            {{__('Back')}}
        </x-tomato-admin-button>
        <x-tomato-admin-button confirm danger method="POST" type="link" href="{{route('admin.pages.clear', $model->id)}}">
            <x-heroicon-s-trash class="w-4 h-4" />
            {{__('Clear Page Sections')}}
        </x-tomato-admin-button>
        <a class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm shadow-sm focus:ring-white filament-page-button-action bg-warning-600 hover:bg-warning-500 focus:bg-warning-700 focus:ring-offset-warning-700 text-white border-transparent" target="_blank" href="{{url($model->slug)}}">
            <x-heroicon-s-eye class="w-4 h-4" />
            {{__('Preview')}}
        </a>
    </x-slot:buttons>
    <x-splade-form method="POST" action="{{route('admin.forms.options', $model->id)}}">
        @foreach($sections as $key=>$section)
            <h1 class="text-lg font-bold border-b border-gray-200 py-2 my-4">{{\Illuminate\Support\Str::of($key)->ucfirst()}}</h1>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 mb-4">
                @foreach($section as $item)
                    <x-splade-link method="POST" href="{{route('admin.pages.sections', $model->id)}}" :data="['section' => $item->id]" class="cursor-pointer flex flex-col jusitify-center items-center gap-2 w-full border rounded-lg p-4">
                        <i class="{{$item->icon}} bx-md"></i>
                        <h3 class="text-sm text-center">{{\Illuminate\Support\Str::of($item->key)->replace('_', ' ')->ucfirst()}}</h3>
                    </x-splade-link>
                @endforeach
            </div>
        @endforeach

        @if(count($model->sections))
            <div class="border rounded-lg overflow-hidden">
                @foreach($model->sections()->orderBy('order')->withPivot('id')->get() as $option)
                    @if($option->form)
                        <x-splade-link  href="{{route('admin.pages.meta', $model->id) . '?section='. $option->id}}" modal>
                            @include($option->view, ['page' => $model, 'section' => $option])
                        </x-splade-link>
                    @else
                        <x-splade-data :default="['showOptions' => false]">
                            <div @click.prevent="data.showOptions = !data.showOptions">
                                @include($option->view, ['page' => $model, 'section' => $option])
                            </div>
                            <div class="my-4" v-if="data.showOptions">
                                <x-tomato-admin-button danger confirm method="DELETE" :data="['section' => $option->pivot->id]" href="{{route('admin.pages.remove', $model->id)}}">
                                    {{__('Remove Section')}}
                                </x-tomato-admin-button>
                            </div>
                        </x-splade-data>
                    @endif
                @endforeach
            </div>
        @else
            <div class="cursor-move flex flex-col gap-4 items-center text-center justifiy-center w-full border rounded-lg p-4">
                <x-heroicon-s-arrows-pointing-in class="w-12 h-12" />
                {{__('Click On Any Section To Add It Here')}}
            </div>
        @endif
    </x-splade-form>
</x-tomato-admin-container>
