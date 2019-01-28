<div>
    <label for="tag" >负责 人:</label><input type="text" name="person"  value="{{$infoData['person']}}" {{$readonly}} autofocus>
    <label for="tag" >介绍 人:</label><input type="text"  name="introducer" value="{{$infoData['introducer']}}" {{$readonly}} autofocus>
</div>
<div>
  <label for="tag" >备注:</label>
  <textarea name="remarks"  {{$readonly}} >{{$infoData['remarks']}}</textarea>
</div>
