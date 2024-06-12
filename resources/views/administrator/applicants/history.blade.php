<!-- history.blade.php -->
<div>
{{--    <h3>Notes History</h3>--}}
    <ul>
        @foreach($notes as $note)
            <li>
                <div class="note">
                    <p><strong>Added By:</strong> {{ $note->user->fullName }} : <strong>Date:</strong> {{ $note->module_note_added_date }}  <strong>Time:</strong> {{ $note->module_note_added_time }}</p>

                </div>
                <div class="note-details">
                    <p><strong>Details:</strong> {{ $note->details }}</p>
                </div>
            </li>
        @endforeach
    </ul>
</div>
