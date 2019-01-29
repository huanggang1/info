<div>
    <label for="tag" >年   级:</label><input type="text" name="grade" value="{{$infoData['grade']}}" {{$readonly}} autofocus>
    <label for="tag" >考生 号:</label><input type="text"  name="examineeNum" value="{{$infoData['examineeNum']}}" {{$readonly}} autofocus>
    <label for="tag" >成   绩:</label><input type="text"  name="achievement" value="{{$infoData['achievement']}}" {{$readonly}} autofocus>
    <label for="tag" >学   号:</label><input type="text"  name="studentNum" value="{{$infoData['studentNum']}}" {{$readonly}} autofocus>
</div>
<div>
    <label for="tag" >报名日期:</label><input type="text" name="applyTime" value="{{$infoData['applyTime']}}" {{$readonly}} autofocus>
    <label for="tag" >初始学校:</label><input type="text"  name="initialSchool" value="{{$infoData['initialSchool']}}" {{$readonly}} autofocus>
    <label for="tag" >层   次:</label><input type="text"  name="level" value="{{$infoData['level']}}" {{$readonly}} autofocus>
    <label for="tag" >学习形式:</label><input type="text" name="studyForm" value="{{$infoData['studyForm']}}" {{$readonly}} autofocus>
</div>
<div>
    <label for="tag" >报考学校:</label>
    <select {{$readonly}} name="applySchool">
        <option value="">--请选择--</option>
        @foreach($data as $v)
        <option value="{{$v['id']}}" @if ($infoData['applySchool']== $v['id']) selected @endif>{{$v['name']}}</option>
        @endforeach
    </select>
    <label for="tag" >报考专业:</label><input type="text"  name="applyProfession" value="{{$infoData['applyProfession']}}" {{$readonly}} autofocus>
    <label for="tag" >核对地址:</label><input type="text"  name="checkAddress" value="{{$infoData['checkAddress']}}" {{$readonly}} autofocus>
    <label for="tag" >预留字段:</label><input type="text"  name="enterFIeld" value="{{$infoData['enterFIeld']}}" {{$readonly}} autofocus>
</div>
<div>
    <label for="tag" >个人履历:</label>
    <textarea name="personalResume" {{$readonly}}>{{$infoData['personalResume']}}</textarea>
   
</div>
