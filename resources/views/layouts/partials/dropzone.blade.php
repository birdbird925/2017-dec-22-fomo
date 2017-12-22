<form action="/image/upload" class="dropzone" id="dropzone-form" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="isProductImage" value="1">
    <div class="dz-message"></div>
    <div class="fallback">
        <input type="file" name="image" multiple>
    </div>
    {{-- .fallback --}}
    @if(isset($dropzoneImage))
        @foreach($dropzoneImage as $image)
            <div class="dz-preview dz-processing dz-image-preview dz-complete" image-id="{{ $image->id }}">
                <div class="dz-image-preview">
                    <img id="{{ $image->id }}" src="/{{ $image->path }}thumb-{{ $image->name }}" draggable="true" ondragstart="drag(event)" ondrop="dropToSwap(event)" ondragover="allowDrop(event)">
                </div>
                <a href="#" class="dz-remove" data-dz-remove="">Remove</a>
            </div>
        @endforeach
    @endif
</form>
