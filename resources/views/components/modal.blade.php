<div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true">
    <div {{$title->attributes->class(['modal-dialog'])}}>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$title}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="d-grid gap-3" id="{{$formId}}" onsubmit="return this.{{$formId}}.disabled=true;" {{ $attributes }}>
                    @csrf
                    {{$slot}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <input {{$button->attributes->class(['btn'])}} type="submit" form="{{$formId}}" value="{{$button}}">
            </div>
        </div>
    </div>
</div>
