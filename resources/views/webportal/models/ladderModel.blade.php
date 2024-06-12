<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header pt-2 pb-2">
                <h6 class="modal-title" id="exampleModalLabel"><b>Winter 2021  - Season Ladders</b></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if($ladders)
                    @foreach($ladders as $ladder)
                        <div class="col-lg-6">
                            <div class="example">
                                <label class="checkbox-button">
                                    <input type="checkbox" class="checkbox-button__input ladder-checkbox" id="{{'ladderId'.$ladder->id}}" name="{{'ladderId'.$ladder->id}}" value="{{$ladder->id}}">
                                    <span class="checkbox-button__control"></span>
                                    <span class="checkbox-button__label">{{$ladder->name}} </span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="modal-footer pt-1 pb-1">
                <a class="btn btn-sm btn-success ladderModelButton" href="#">Buy Now</a>
            </div>
        </div>
    </div>
</div>