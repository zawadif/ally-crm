
@foreach($messages as $message)
<li class="replies">
    <img src="http://emilcarlsson.se/assets/mikeross.png" alt="" width="25px"  height="25px;"/>
    <p>{{$message['lastMessage']}}</p>
    <sub style="text-align: center;display: block;margin-top: 59px;margin-left: 107px;color: #583192;">12:52PM</sub>
</li>

@endforeach




